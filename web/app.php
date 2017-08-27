<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

// Ensure UTF-8 is used in string operations
setlocale(LC_CTYPE, 'C.UTF-8');

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

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__ . '/../vendor/autoload.php';

if ($useDebugging) {
    Debug::enable();
}

$kernel = new AppKernel($environment, $useDebugging);

// Depending on the SYMFONY_HTTP_CACHE environment variable, tells whether the internal HTTP Cache mechanism is to be used.
// If not set, or "", it is auto activated if _not_ in "dev" environment.
if (($useHttpCache = getenv('SYMFONY_HTTP_CACHE')) === false || $useHttpCache === '') {
    $useHttpCache = $environment !== 'dev';
}

// Load HTTP Cache ...
if ($useHttpCache) {
    // The standard HttpCache implementation can be overridden by setting the SYMFONY_HTTP_CACHE_CLASS environment variable.
    // NOTE: Make sure to setup composer config so it is *autoloadable*, or use "SYMFONY_CLASSLOADER_FILE" for this.
    if ($httpCacheClass = getenv('SYMFONY_HTTP_CACHE_CLASS')) {
        $kernel = new $httpCacheClass($kernel);
    } else {
        $kernel = new AppCache($kernel);
    }
}

$request = Request::createFromGlobals();

// If you are behind one or more trusted reverse proxies, you might want to set them in SYMFONY_TRUSTED_PROXIES environment
// variable in order to get correct client IP
if ($trustedProxies = getenv('SYMFONY_TRUSTED_PROXIES')) {
    Request::setTrustedProxies(explode(',', $trustedProxies));
}

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
