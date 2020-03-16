<?php

use App\CacheKernel;
use App\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/config/bootstrap.php';

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);

// Depending on the APP_HTTP_CACHE environment variable, tells whether the internal HTTP Cache mechanism is to be used.
// Recommendation is to use Varnish over this, for performance and being able to setup cluster if you need to.
// If not set, or "", it is auto activated if _not_ in "dev" environment.
if (($useHttpCache = getenv('APP_HTTP_CACHE')) === false || $useHttpCache === '') {
    $useHttpCache = $_SERVER['APP_ENV'] !== 'dev';
}

// Load internal HTTP Cache, aka Symfony Proxy, if enabled
if ($useHttpCache) {
    $kernel = new CacheKernel($kernel);

    // Needed when using Synfony proxy, see: http://symfony.com/doc/3.4/reference/configuration/framework.html#http-method-override
    Request::enableHttpMethodParameterOverride();
}
$request = Request::createFromGlobals();

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
