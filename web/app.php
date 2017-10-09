<?php

use Symfony\Component\HttpFoundation\Request;

// Ensure UTF-8 is used in string operations
setlocale(LC_CTYPE, 'C.UTF-8');
require __DIR__ . '/../vendor/autoload.php';

$kernel = new AppKernel('prod', false);

// Depending on the SYMFONY_HTTP_CACHE environment variable, tells whether the internal HTTP Cache mechanism is to be used.
// If not set, or "", it is auto activated
if (($useHttpCache = getenv('SYMFONY_HTTP_CACHE')) === false || $useHttpCache === '') {
    $useHttpCache = true;
}

if ($useHttpCache) {
    $kernel = new AppCache($kernel);

    // When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
    Request::enableHttpMethodParameterOverride();

    // If you are behind one or more trusted reverse proxies, you might want to set them in SYMFONY_TRUSTED_PROXIES environment
    // variable in order to get correct client IP
    if ($trustedProxies = getenv('SYMFONY_TRUSTED_PROXIES')) {
        Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL);
    }
}

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
