<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

// Disable the PHAR stream wrapper as it is insecure
if (in_array('phar', stream_get_wrappers())) {
    stream_wrapper_unregister('phar');
}

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

// Depending on SYMFONY_CLASSLOADER_FILE use custom class loader, otherwise use normal autoload and optionally bootstrap cache on PHP 5 when not in debug mode
if ($loaderFile = getenv('SYMFONY_CLASSLOADER_FILE')) {
    require_once $loaderFile;
} else {
    require_once __DIR__ . '/../app/autoload.php';
    if (!$useDebugging && PHP_VERSION_ID < 70000) {
        require_once __DIR__ . '/../app/bootstrap.php.cache';
    }
}

if ($useDebugging) {
    Debug::enable();
}

$kernel = new AppKernel($environment, $useDebugging);

// we don't want to use the class cache if we are in a debug session
// Also not on PHP7+ where it does not give benefit (if using composer optimize autoload), only downsides remains
if (!$useDebugging && PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}

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
    if ($trustedProxies === 'TRUST_REMOTE') {
        Request::setTrustedProxies([$request->server->get('REMOTE_ADDR')]);
    } else {
        Request::setTrustedProxies(explode(',', $trustedProxies));
    }
}

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
