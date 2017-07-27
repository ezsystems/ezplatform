// Our Backend - Assuming that web server is listening on port 80
// Replace the host to fit your setup
//
// For additional example see: doc/docker/entrypoint/varnish/parameters.vcl

backend ezplatform {
    .host = "127.0.0.1";
    .port = "80";
}

// ACL for invalidators IP
acl invalidators {
    "127.0.0.1";
    "192.168.0.0"/16;
}

// ACL for debuggers IP
acl debuggers {
    "127.0.0.1";
    "192.168.0.0"/16;
}
