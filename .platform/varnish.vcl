// Varnish VCL for:
// - Varnish 5.1 or higher (6.0LTS recommended, and is what we mainly test against)
//   - Varnish xkey vmod (via varnish-modules package, or via Varnish Plus)
// - eZ Platform 2.5LTS or higher with ezplatform-http-cache (this) bundle
//
// Make sure to at least adjust default parameters.yml, defaults there reflect our testing needs with docker.

// Not applicable on Platform.sh:
//vcl 4.1;
//import std;
import xkey;

// For customizing your backend and acl rules see parameters.vcl
// Includes not available on Platform.sh
//include "parameters.vcl";
acl invalidators {
    "127.0.0.1";
    "192.168.0.0"/16;
}

// ACL for debuggers IP
acl debuggers {
    "127.0.0.1";
    "192.168.0.0"/16;
}

// Called at the beginning of a request, after the complete request has been received
sub vcl_recv {
    // Set the backend
    //set req.backend_hint = ezplatform;
    // Platform.sh specific:
    set req.backend_hint = app.backend();

    // Add a Surrogate-Capability header to announce ESI support.
    set req.http.Surrogate-Capability = "abc=ESI/1.0";

    // Ensure that the Symfony Router generates URLs correctly with Varnish
    if (req.http.X-Forwarded-Proto == "https" ) {
        set req.http.X-Forwarded-Port = "443";
    } else {
        set req.http.X-Forwarded-Port = "80";
    }

    // Trigger cache purge if needed
    call ez_purge;

    // Don't cache requests other than GET and HEAD.
    if (req.method != "GET" && req.method != "HEAD") {
        return (pass);
    }

    // Don't cache Authenticate & Authorization
    // You may remove this when using REST API with basic auth.
    if (req.http.Authenticate || req.http.Authorization) {
        if (client.ip ~ debuggers) {
            set req.http.X-Debug = "Not Cached according to configuration (Authorization)";
        }
        return (hash);
    }

    // Remove all cookies besides Session ID, as JS tracker cookies and so will make the responses effectively un-cached
    if (req.http.cookie) {
        set req.http.cookie = ";" + req.http.cookie;
        set req.http.cookie = regsuball(req.http.cookie, "; +", ";");
        set req.http.cookie = regsuball(req.http.cookie, ";(eZSESSID[^=]*)=", "; \1=");
        set req.http.cookie = regsuball(req.http.cookie, ";[^ ][^;]*", "");
        set req.http.cookie = regsuball(req.http.cookie, "^[; ]+|[; ]+$", "");

        if (req.http.cookie == "") {
            // If there are no more cookies, remove the header to get page cached.
            unset req.http.cookie;
        }
    }

    // Do a standard lookup on assets (these don't vary by user context hash)
    // Note that file extension list below is not extensive, so consider completing it to fit your needs.
    if (req.url ~ "\.(css|js|gif|jpe?g|bmp|png|tiff?|ico|img|tga|wmf|svg|swf|ico|mp3|mp4|m4a|ogg|mov|avi|wmv|zip|gz|pdf|ttf|eot|wof)$") {
        return (hash);
    }

    // Sort the query string for cache normalization.
    set req.url = std.querysort(req.url);

    // Retrieve client user context hash and add it to the forwarded request.
    call ez_user_context_hash;

    // If it passes all these tests, do a lookup anyway.
    return (hash);
}

// Called when a cache lookup is successful. The object being hit may be stale: It can have a zero or negative ttl with only grace or keep time left.
sub vcl_hit {
   if (obj.ttl >= 0s) {
       // A pure unadulterated hit, deliver it
       return (deliver);
   }

   if (obj.ttl + obj.grace > 0s) {
       // Object is in grace, logic below in this block is what differs from default:
       // https://varnish-cache.org/docs/5.2/users-guide/vcl-grace.html#grace-mode
       if (!std.healthy(req.backend_hint)) {
           // Service is unhealthy, deliver from cache
           return (deliver);
       } else if (req.http.cookie) {
           // Request it by a user with session, refresh the cache to avoid issues for editors and forum users
           return (miss);
       }

       // By default deliver cache, automatically triggers a background fetch
       return (deliver);
   }

   // fetch & deliver once we get the result
   return (miss);
}

// Called when the requested object has been retrieved from the backend
sub vcl_backend_response {
    if (bereq.http.accept ~ "application/vnd.fos.user-context-hash"
        && beresp.status >= 500
    ) {
        return (abandon);
    }

    // Check for ESI acknowledgement and remove Surrogate-Control header
    if (beresp.http.Surrogate-Control ~ "ESI/1.0") {
        unset beresp.http.Surrogate-Control;
        set beresp.do_esi = true;
    }

    // Make Varnish keep all objects for up to 1 hour beyond their TTL, see vcl_hit for Request logic on this
    set beresp.grace = 1h;

    // Compressing the content
    if (beresp.http.Content-Type ~ "application/javascript"
        || beresp.http.Content-Type ~ "application/json"
        || beresp.http.Content-Type ~ "application/vnd.ms-fontobject"
        || beresp.http.Content-Type ~ "application/vnd.ez.api"
        || beresp.http.Content-Type ~ "application/x-font-ttf"
        || beresp.http.Content-Type ~ "image/svg+xml"
        || beresp.http.Content-Type ~ "text/css"
        || beresp.http.Content-Type ~ "text/plain"
    ) {
        set beresp.do_gzip = true;
    }
}

// Handle purge
// You may add FOSHttpCacheBundle tagging rules
// See http://foshttpcache.readthedocs.org/en/latest/varnish-configuration.html#id4
sub ez_purge {
    // Retrieve purge token, needs to be here due to restart, match for PURGE method done within
    call ez_invalidate_token;

    # Support how purging was done in earlier versions, this is deprecated and here just for BC for code still using it
    if (req.method == "BAN") {
        call ez_purge_acl;

        if (req.http.X-Location-Id) {
            ban("obj.http.X-Location-Id ~ " + req.http.X-Location-Id);
            if (client.ip ~ debuggers) {
                set req.http.X-Debug = "Ban done for content connected to LocationId " + req.http.X-Location-Id;
            }
            return (synth(200, "Banned"));
        }
    }

    if (req.method == "PURGE") {
        call ez_purge_acl;

        # If http header "key" is set, we assume purge is on key and you have Varnish xkey installed
        if (req.http.key) {
            # By default we recommend using soft purge to respect grace time, if you need to hard purge use:
            # set req.http.n-gone = xkey.purge(req.http.key);
            set req.http.n-gone = xkey.softpurge(req.http.key);

            return (synth(200, "Invalidated "+req.http.n-gone+" objects"));
        }

        # if not, then this is a normal purge by url
        return (purge);
    }
}

sub ez_purge_acl {
    if (req.http.x-invalidate-token) {
        if (req.http.x-invalidate-token != req.http.x-backend-invalidate-token) {
            return (synth(405, "Method not allowed"));
        }
    } else if  (!client.ip ~ invalidators) {
        return (synth(405, "Method not allowed"));
    }
}

// Sub-routine to get client user context hash, used to for being able to vary page cache on user rights.
sub ez_user_context_hash {
    // Prevent tampering attacks on the hash mechanism
    if (req.restarts == 0
        && (req.http.accept ~ "application/vnd.fos.user-context-hash"
            || req.http.x-user-hash
        )
    ) {
        return (synth(400));
    }

    if (req.restarts == 0 && (req.method == "GET" || req.method == "HEAD")) {
        // Backup accept header, if set
        if (req.http.accept) {
            set req.http.x-fos-original-accept = req.http.accept;
        }
        set req.http.accept = "application/vnd.fos.user-context-hash";

        // Backup original URL
        set req.http.x-fos-original-url = req.url;
        set req.url = "/_fos_user_context_hash";

        // Force the lookup, the backend must tell not to cache or vary on all
        // headers that are used to build the hash.
        return (hash);
    }

    // Rebuild the original request which now has the hash.
    if (req.restarts > 0
        && req.http.accept == "application/vnd.fos.user-context-hash"
    ) {
        set req.url = req.http.x-fos-original-url;
        unset req.http.x-fos-original-url;
        if (req.http.x-fos-original-accept) {
            set req.http.accept = req.http.x-fos-original-accept;
            unset req.http.x-fos-original-accept;
        } else {
            // If accept header was not set in original request, remove the header here.
            unset req.http.accept;
        }

        // Force the lookup, the backend must tell not to cache or vary on the
        // user context hash to properly separate cached data.

        return (hash);
    }
}

// Sub-routine to get invalidate token.
sub ez_invalidate_token {
    // Prevent tampering attacks on the token mechanisms
    if (req.restarts == 0
        && (req.http.accept ~ "application/vnd.ezplatform.invalidate-token"
            || req.http.x-backend-invalidate-token
        )
    ) {
        return (synth(400));
    }

    if (req.restarts == 0 && req.method == "PURGE" && req.http.x-invalidate-token) {
        set req.http.accept = "application/vnd.ezplatform.invalidate-token";

        set req.url = "/_ez_http_invalidatetoken";

        // Force the lookup
        return (hash);
    }

    // Rebuild the original request which now has the invalidate token.
    if (req.restarts > 0
        && req.http.accept == "application/vnd.ezplatform.invalidate-token"
    ) {
        set req.url = "/";
        set req.method = "PURGE";
        unset req.http.accept;
    }
}

sub vcl_deliver {
    // On receiving the invalidate token response, copy the invalidate token to the original
    // request and restart.
    if (req.restarts == 0
        && resp.http.content-type ~ "application/vnd.ezplatform.invalidate-token"
    ) {
        set req.http.x-backend-invalidate-token = resp.http.x-invalidate-token;

        return (restart);
    }

    // On receiving the hash response, copy the hash header to the original
    // request and restart.
    if (req.restarts == 0
        && resp.http.content-type ~ "application/vnd.fos.user-context-hash"
    ) {
        set req.http.x-user-hash = resp.http.x-user-hash;

        return (restart);
    }

    // If we get here, this is a real response that gets sent to the client.

    // Remove the vary on user context hash, this is nothing public. Keep all
    // other vary headers.
    if (resp.http.Vary ~ "X-User-Hash") {
        set resp.http.Vary = regsub(resp.http.Vary, "(?i),? *X-User-Hash *", "");
        set resp.http.Vary = regsub(resp.http.Vary, "^, *", "");
        if (resp.http.Vary == "") {
            unset resp.http.Vary;
        }

        // If we vary by user hash, we'll also adjust the cache control headers going out by default to avoid sending
        // large ttl meant for Varnish to shared proxies and such. We assume only session cookie is left after vcl_recv.
        if (req.http.cookie) {
            // When in session where we vary by user hash we by default avoid caching this in shared proxies & browsers
            // For browser cache with it revalidating against varnish, use for instance "private, no-cache" instead
            set resp.http.cache-control = "private, no-cache, no-store, must-revalidate";
        } else if (resp.http.cache-control ~ "public") {
            // For non logged in users we allow caching on shared proxies (mobile network accelerators, planes, ...)
            // But only for a short while, as there is no way to purge them
            set resp.http.cache-control = "public, s-maxage=600, stale-while-revalidate=300, stale-if-error=300";
        }
    }

    if (client.ip ~ debuggers) {
        // Add X-Cache header if debugging is enabled
        if (obj.hits > 0) {
            set resp.http.X-Cache = "HIT";
            set resp.http.X-Cache-Hits = obj.hits;
            set resp.http.X-Cache-TTL = obj.ttl;
        } else {
            set resp.http.X-Cache = "MISS";
        }
    } else {
        // Remove tag headers when delivering to non debug client
        unset resp.http.xkey;
        // Sanity check to prevent ever exposing the hash to a non debug client.
        unset resp.http.x-user-hash;
    }
}
