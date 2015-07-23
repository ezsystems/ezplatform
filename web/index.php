<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

// Environment is taken from "ENVIRONMENT" variable, if not set, defaults to "prod"
$environment = getenv('ENVIRONMENT');
if ($environment === false) {
    $environment = 'prod';
}

// Depending on the USE_DEBUGGING environment variable, tells whether Symfony should be loaded with debugging.
// If not set it is activated if in "dev" environment.
if (($useDebugging = getenv('USE_DEBUGGING')) === false) {
    $useDebugging = $environment === 'dev';
}

// Depending on CUSTOM_CLASSLOADER_FILE use custom class loader, otherwise use bootstrap cache, or autoload in debug
if (($loaderFile = getenv('CUSTOM_CLASSLOADER_FILE')) !== false) {
    require_once $loaderFile;
} elseif ($useDebugging) {
    require_once __DIR__ . '/../ezpublish/autoload.php';
} else {
    require_once __DIR__ . '/../ezpublish/bootstrap.php.cache';
}

require_once __DIR__ . '/../ezpublish/EzPublishKernel.php';

if ($useDebugging) {
    Debug::enable();
}

$kernel = new EzPublishKernel($environment, $useDebugging);

// we don't want to use the classes cache if we are in a debug session
if (!$useDebugging) {
    $kernel->loadClassCache();
}

// Depending on the USE_HTTP_CACHE environment variable, tells whether the internal HTTP Cache mechanism is to be used.
// If not set it is activated if not in "dev" environment.
if (($useHttpCache = getenv('USE_HTTP_CACHE')) === false) {
    $useHttpCache = $environment !== 'dev';
}

// Load HTTP Cache ...
if ($useHttpCache) {
    // The standard HttpCache implementation can be overridden by setting the HTTP_CACHE_CLASS environment variable.
    // Make sure to setup composer config so it is *autoloadable*, or fallback to use "CUSTOM_CLASSLOADER_FILE"
    if (($httpCacheClass = getenv('HTTP_CACHE_CLASS')) !== false) {
        $kernel = new $httpCacheClass($kernel);
    } else {
        require_once __DIR__ . '/../ezpublish/EzPublishCache.php';
        $kernel = new EzPublishCache($kernel);
    }
}

$request = Request::createFromGlobals();

// If you are behind one or more trusted reverse proxies, you might want to set them in TRUSTED_PROXIES environment
// variable in order to get correct client IP
if (($trustedProxies = getenv('TRUSTED_PROXIES')) !== false) {
    Request::setTrustedProxies(explode(',', $trustedProxies));
}

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
