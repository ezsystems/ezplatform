// Varnish VCL for:
// - Varnish 4.1 or higher with xkey vmod (via varnish-modules package, or via Varnish Plus)
// - eZ Platform 1.8 or higher (configured for Varnish, incl with Symfony HttpCache Proxy disabled)
//
// Complete VCL example, further reading on:
// - https://symfony.com/doc/current/http_cache/varnish.html
// - https://foshttpcache.readthedocs.io/en/stable/varnish-configuration.html
// - https://github.com/varnish/varnish-modules/blob/master/docs/vmod_xkey.rst
// - https://www.varnish-cache.org/docs/trunk/users-guide/vcl.html
//
// Make sure to at least adjust default domain/ip values on backend, invalidators & debuggers, current values are set
// for default bundled docker containers for testing use and needs to reflect your setup instead.

vcl 4.0;
import std;
import xkey;

// For customizing your backend and acl rules see parameters.yml
include "parameters.vcl";

// Called at the beginning of a request, after the complete request has been received
sub vcl_recv {

    // Set the backend
    set req.backend_hint = ezplatform;

    // Advertise Symfony for ESI support
    set req.http.Surrogate-Capability = "abc=ESI/1.0";

    // Varnish, in its default configuration, sends the X-Forwarded-For header but does not filter out Forwarded header
    unset req.http.Forwarded;

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

    // Do a standard lookup on assets (these don't vary by user context hash)
    // Note that file extension list below is not extensive, so consider completing it to fit your needs.
    if (req.url ~ "\.(css|js|gif|jpe?g|bmp|png|tiff?|ico|img|tga|wmf|svg|swf|ico|mp3|mp4|m4a|ogg|mov|avi|wmv|zip|gz|pdf|ttf|eot|wof)$") {
        return (hash);
    }

    // Retrieve client user context hash and add it to the forwarded request.
    call ez_user_context_hash;

    // If it passes all these tests, do a lookup anyway.
    return (hash);
}

// Called when a cache lookup is successful. The object being hit may be stale: It can have a zero or negative ttl with only grace or keep time left.
sub vcl_hit {
   if (obj.ttl >= 0s) {
       // A pure unadultered hit, deliver it
       return (deliver);
   }

   if (obj.ttl + obj.grace > 0s) {
       // Object is in grace, logic below in this block is what differs from default:
       // https://varnish-cache.org/docs/5.0/users-guide/vcl-grace.html#grace-mode
       if (!std.healthy(req.backend_hint)) {
           // Service is unhealthy, deliver from cache
           return (deliver);
       } else if (req.url ~ "^/api/ezp/v2" && req.http.referer ~ "/ez$") {
           // Request is for Platform UI for REST API, fetch it
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
}

// Handle purge
// You may add FOSHttpCacheBundle tagging rules
// See http://foshttpcache.readthedocs.org/en/latest/varnish-configuration.html#id4
sub ez_purge {
    if (req.method == "PURGE") {
        if (!client.ip ~ invalidators) {
            return (synth(405, "Method not allowed"));
        }

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

        // Backup cookie heder if set and only forward session ID to hash lookup, as hash cache will vary on cookie.
        if (req.http.cookie) {
            set req.http.x-fos-original-cookie = req.http.cookie;
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

        if (req.http.x-fos-original-cookie) {
            set req.http.cookie = req.http.x-fos-original-cookie;
            unset req.http.x-fos-original-cookie;
        }

        // Force the lookup, the backend must tell not to cache or vary on the
        // user context hash to properly separate cached data.

        return (hash);
    }
}

sub vcl_deliver {
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
    set resp.http.Vary = regsub(resp.http.Vary, "(?i),? *X-User-Hash *", "");
    set resp.http.Vary = regsub(resp.http.Vary, "^, *", "");
    if (resp.http.Vary == "") {
        unset resp.http.Vary;
    }

    // Sanity check to prevent ever exposing the hash to a client.
    unset resp.http.x-user-hash;

    if (client.ip ~ debuggers) {
        if (resp.http.X-Varnish ~ " ") {
            set resp.http.X-Cache = "HIT";
        } else {
            set resp.http.X-Cache = "MISS";
        }
    } else {
        // Remove tag headers when delivering to non debug client
        unset resp.http.xkey;
    }
}
