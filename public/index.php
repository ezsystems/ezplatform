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

$trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? '';

// specific for platform.sh deployed to both with and without varnish access, when you should not trust REMOTE_ADDR
// platform sh is putting real client ip into REMOTE_ADDR instead of proxy IP, so we can't verify it
// see https://github.com/symfony/symfony/issues/26006
// but we can be sure that it's coming from varnish if REMOTE_ADDR is second from the end of 'x-forwarded-for'
if (
    isset($_SERVER['PLATFORM_PROJECT_ENTROPY'], $_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['HTTP_X_CLIENT_IP'])
    && $_SERVER['HTTP_X_CLIENT_IP'] === $_SERVER['REMOTE_ADDR']
) {
    // array_unique to make sure user didn't send own IP in x-forwarded-for
    // strtolower for ipv6 uniqueness
    $reverseIps = array_unique(array_reverse(array_map(
        'trim',
        explode(',', strtolower($_SERVER['HTTP_X_FORWARDED_FOR']))
    )));
    if (isset($reverseIps[1]) && $reverseIps[1] === $_SERVER['REMOTE_ADDR']) {
        // restore real remote addr, overrided by ngx_http_realip_module, and trust it
        $trustedProxies = ($trustedProxies ? ',' : '') . $reverseIps[0];
        $_SERVER['REMOTE_ADDR'] = $reverseIps[0];
    }
}

if ($trustedProxies) {
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
