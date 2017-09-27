// Varnish 3 style - eZ 5.4+ / 2014.09+
// Complete VCL example
// DEPRECATED: For faster, and over time more stable setup look to ezplatform-http-cache and Varnish xkey use.

// For customizing your backend and acl rules see parameters.yml
include "parameters.vcl";

// Called at the beginning of a request, after the complete request has been received
sub vcl_recv {

    // Set the backend
    set req.backend = ezplatform;

    // Advertise Symfony for ESI support
    set req.http.Surrogate-Capability = "abc=ESI/1.0";

    // Add a unique header containing the client address (only for master request)
    // Please note that /_fragment URI can change in Symfony configuration
    if (!req.url ~ "^/_fragment") {
        if (req.http.x-forwarded-for) {
            set req.http.X-Forwarded-For = req.http.X-Forwarded-For + ", " + client.ip;
        } else {
            set req.http.X-Forwarded-For = client.ip;
        }
    }

    // Trigger cache purge if needed
    call ez_purge;

    // Don't cache requests other than GET and HEAD.
    if (req.request != "GET" && req.request != "HEAD") {
        return (pass);
    }

    // Normalize the Accept-Encoding headers
    if (req.http.Accept-Encoding) {
        if (req.http.Accept-Encoding ~ "gzip") {
            set req.http.Accept-Encoding = "gzip";
        } elsif (req.http.Accept-Encoding ~ "deflate") {
            set req.http.Accept-Encoding = "deflate";
        } else {
            unset req.http.Accept-Encoding;
        }
    }

    // Don't cache Authenticate & Authorization
    // You may remove this when using REST API with basic auth.
    if (req.http.Authenticate || req.http.Authorization) {
        if (client.ip ~ debuggers) {
            set req.http.X-Debug = "Not Cached according to configuration (Authorization)";
        }
        return(pass);
    }

    // Do a standard lookup on assets
    // Note that file extension list below is not extensive, so consider completing it to fit your needs.
    if (req.url ~ "\.(css|js|gif|jpe?g|bmp|png|tiff?|ico|img|tga|wmf|svg|swf|ico|mp3|mp4|m4a|ogg|mov|avi|wmv|zip|gz|pdf|ttf|eot|wof)$") {
        return (lookup);
    }

    // Retrieve client user hash and add it to the forwarded request.
    call ez_user_hash;

    // If it passes all these tests, do a lookup anyway.
    return (lookup);
}

// Called when the requested object has been retrieved from the backend
sub vcl_fetch {

    if (req.restarts == 0
        && req.http.accept ~ "application/vnd.fos.user-context-hash"
        && beresp.status >= 500
    ) {
        error 503 "Hash error";
    }

    // Optimize to only parse the Response contents from Symfony
    if (beresp.http.Surrogate-Control ~ "ESI/1.0") {
        unset beresp.http.Surrogate-Control;
        set beresp.do_esi = true;
    }

    // Respect the Cache-Control=private header from the backend
    if (beresp.http.Cache-Control ~ "no-cache|no-store|private") {
        set beresp.ttl = 120s;
        return (hit_for_pass);
    }

    return (deliver);
}

// Handle purge
// You may add FOSHttpCacheBundle tagging rules
// See http://foshttpcache.readthedocs.org/en/latest/varnish-configuration.html#id4
sub ez_purge {

    if (req.request == "BAN") {
        if (!client.ip ~ invalidators) {
            error 405 "Method not allowed";
        }

        if (req.http.X-Location-Id) {
            ban( "obj.http.X-Location-Id ~ " + req.http.X-Location-Id);
            if (client.ip ~ debuggers) {
                set req.http.X-Debug = "Ban done for content connected to LocationId " + req.http.X-Location-Id;
            }
            error 200 "Banned";
        }
    }
}

// Sub-routine to get client user hash, for context-aware HTTP cache.
sub ez_user_hash {

    // Prevent tampering attacks on the hash mechanism
    if (req.restarts == 0
        && (req.http.accept ~ "application/vnd.fos.user-context-hash"
            || req.http.x-user-hash
        )
    ) {
        error 400;
    }

    if (req.restarts == 0 && (req.request == "GET" || req.request == "HEAD")) {
        // Get User (Context) hash, for varying cache by what user has access to.
        // https://doc.ez.no/display/EZP/Context+aware+HTTP+cache

        // Anonymous user w/o session => Use hardcoded anonymous hash to avoid backend lookup for hash
        if (req.http.Cookie !~ "eZSESSID" && !req.http.authorization) {
            // You may update this hash with the actual one for anonymous user
            // to get a better cache hit ratio across anonymous users.
            // Note: You should then update it every time anonymous user rights change.
            set req.http.X-User-Hash = "38015b703d82206ebc01d17a39c727e5";
        }
        // Pre-authenticate request to get shared cache, even when authenticated
        else {
            set req.http.x-fos-original-url    = req.url;
            set req.http.x-fos-original-accept = req.http.accept;
            set req.http.x-fos-original-cookie = req.http.cookie;
            // Clean up cookie for the hash request to only keep session cookie, as hash cache will vary on cookie.
            set req.http.cookie = ";" + req.http.cookie;
            set req.http.cookie = regsuball(req.http.cookie, "; +", ";");
            set req.http.cookie = regsuball(req.http.cookie, ";(eZSESSID[^=]*)=", "; \1=");
            set req.http.cookie = regsuball(req.http.cookie, ";[^ ][^;]*", "");
            set req.http.cookie = regsuball(req.http.cookie, "^[; ]+|[; ]+$", "");

            set req.http.accept = "application/vnd.fos.user-context-hash";
            set req.url = "/_fos_user_context_hash";

            // Force the lookup, the backend must tell how to cache/vary response containing the user hash

            return (lookup);
        }
    }

    // Rebuild the original request which now has the hash.
    if (req.restarts > 0
        && req.http.accept == "application/vnd.fos.user-context-hash"
    ) {
        set req.url         = req.http.x-fos-original-url;
        set req.http.accept = req.http.x-fos-original-accept;
        set req.http.cookie = req.http.x-fos-original-cookie;

        unset req.http.x-fos-original-url;
        unset req.http.x-fos-original-accept;
        unset req.http.x-fos-original-cookie;

        // Force the lookup, the backend must tell not to cache or vary on the
        // user hash to properly separate cached data.

        return (lookup);
    }
}

sub vcl_deliver {
    // On receiving the hash response, copy the hash header to the original
    // request and restart.
    if (req.restarts == 0
        && resp.http.content-type ~ "application/vnd.fos.user-context-hash"
        && resp.status == 200
    ) {
        set req.http.x-user-hash = resp.http.x-user-hash;

        return (restart);
    }

    // If we get here, this is a real response that gets sent to the client.

    // Remove the vary on context user hash, this is nothing public. Keep all
    // other vary headers.
    set resp.http.Vary = regsub(resp.http.Vary, "(?i),? *x-user-hash *", "");
    set resp.http.Vary = regsub(resp.http.Vary, "^, *", "");
    if (resp.http.Vary == "") {
        unset resp.http.Vary;
    }

    // Sanity check to prevent ever exposing the hash to a client.
    unset resp.http.x-user-hash;

    if (client.ip ~ debuggers) {
        if (obj.hits > 0) {
            set resp.http.X-Cache = "HIT";
            set resp.http.X-Cache-Hits = obj.hits;
        } else {
            set resp.http.X-Cache = "MISS";
        }
    }
}
