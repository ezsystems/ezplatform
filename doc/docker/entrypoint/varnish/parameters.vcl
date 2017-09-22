// Custom parameters.vcl for docker use

backend ezplatform {
    .host = "web";
    .port = "80";
}

// ACL for invalidators IP
acl invalidators {
    "127.0.0.1";
// ACL_INVALIDATOR
}

// ACL for debuggers IP
acl debuggers {
    "127.0.0.1";
    "172.16.0.0"/20;
}
