<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

// Disable the PHAR stream wrapper as it is insecure
if (in_array('phar', stream_get_wrappers())) {
    stream_wrapper_unregister('phar');
}

// Ensure UTF-8 is used in string operations
setlocale(LC_CTYPE, 'C.UTF-8');
require __DIR__ . '/../vendor/autoload.php';

// Environment is taken from "SYMFONY_ENV" variable, if not set, defaults to "prod"
$environment = getenv('SYMFONY_ENV');
if ($environment === false) {
    $environment = 'prod';
}

// Depending on the SYMFONY_DEBUG environment variable, tells whether Symfony should be loaded with debugging.
// If not set, or "", it is auto activated if in "dev" environment.
if (($useDebugging = getenv('SYMFONY_DEBUG')) === false || $useDebugging === '') {
    $useDebugging = $environment === 'dev';
}

if ($useDebugging) {
    Debug::enable();
}

$kernel = new AppKernel($environment, $useDebugging);

// Depending on the SYMFONY_HTTP_CACHE environment variable, tells whether the internal HTTP Cache mechanism is to be used.
// Recommendation is to use Varnish over this, for performance and being able to setup cluster if you need to.
// If not set, or "", it is auto activated if _not_ in "dev" environment.
if (($useHttpCache = getenv('SYMFONY_HTTP_CACHE')) === false || $useHttpCache === '') {
    $useHttpCache = $environment !== 'dev';
}

// Load internal HTTP Cache, aka Symfony Proxy, if enabled
if ($useHttpCache) {
    $kernel = new AppCache($kernel);

    // Needed when using Synfony proxy, see: http://symfony.com/doc/3.4/reference/configuration/framework.html#http-method-override
    Request::enableHttpMethodParameterOverride();
}

$request = Request::createFromGlobals();

// Deny request if it contains the frontcontroller script ie. http://example.com/app.php
$frontControllerScript = preg_quote(basename($request->server->get('SCRIPT_FILENAME')));
if (preg_match("<^/([^/]+/)?$frontControllerScript([/?#]|$)>", $request->getRequestUri(), $matches) === 1) {
    http_response_code(400);
    echo('<html><head><title>400 Bad Request</title></head><body><h1>400 Bad Request</h1></center></body></html>');
    die;
}

// If behind one or more trusted proxies, you can set them in SYMFONY_TRUSTED_PROXIES environment variable.
// !! Proxies here refers to load balancers, TLS/Reverse proxies and so on. Which Symfony need to know about to
// work correctly: identify https, allow Varnish to lookup fragment & user hash routes, get correct client ip, ...
//
// NOTE: You'll potentially need to customize these lines for your proxy depending on which forward headers to use!
// SEE: https://symfony.com/doc/3.4/deployment/proxies.html
if ($trustedProxies = getenv('SYMFONY_TRUSTED_PROXIES')) {
    if ($trustedProxies === 'TRUST_REMOTE') {
        Request::setTrustedProxies([$request->server->get('REMOTE_ADDR')], Request::HEADER_X_FORWARDED_ALL);
    } else {
        Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL);
    }
}

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
