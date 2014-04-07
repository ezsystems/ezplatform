<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\Debug\Debug;

// Environment is taken from "ENVIRONMENT" variable, if not set, defaults to "prod"
$environment = getenv( "ENVIRONMENT" );
if ( $environment === false )
{
    $environment = "prod";
}

// Depending on the USE_DEBUGGING environment variable, tells whether Symfony should be loaded with debugging.
// If not set it is activated if in "dev" environment.
if ( ( $useDebugging = getenv( "USE_DEBUGGING" ) ) === false )
{
    $useDebugging = $environment === "dev";
}

if ( $useDebugging )
{
    $loader = require_once __DIR__ . '/../ezpublish/autoload.php';
}
else
{
    $loader = require_once __DIR__ . '/../ezpublish/bootstrap.php.cache';
}

// Depending on the USE_APC_CLASSLOADER environment variable, use APC for autoloading to improve performance.
// If not set it is not used.
if ( getenv( "USE_APC_CLASSLOADER" ) )
{
    $prefix = getenv( "APC_CLASSLOADER_PREFIX" );

    $loader = new ApcClassLoader( $prefix ?: "ezpublish", $loader );
    $loader->register( true );
}

require_once __DIR__ . '/../ezpublish/EzPublishKernel.php';

if ( $useDebugging )
{
    Debug::enable();
}

$kernel = new EzPublishKernel( $environment, $useDebugging );

// we don't want to use the classes cache if we are in a debug session
if ( !$useDebugging )
{
    $kernel->loadClassCache();
}

// Depending on the USE_HTTP_CACHE environment variable, tells whether the internal HTTP Cache mechanism is to be used.
// If not set it is activated if not in "dev" environment.
if ( ( $useHttpCache = getenv( "USE_HTTP_CACHE" ) ) === false )
{
    $useHttpCache = $environment !== "dev";
}
// Load HTTP Cache ...
if ( $useHttpCache )
{
    require_once __DIR__ . '/../ezpublish/EzPublishCache.php';
    $kernel = new EzPublishCache( $kernel );
}

$request = Request::createFromGlobals();

// If you are behind one or more trusted reverse proxies, you might want to set them in TRUSTED_PROXIES environment
// variable in order to get correct client IP
if ( ( $trustedProxies = getenv( "TRUSTED_PROXIES" ) ) !== false )
{
    Request::setTrustedProxies( explode( ",", $trustedProxies ) );
}

$response = $kernel->handle( $request );
$response->send();
$kernel->terminate( $request, $response );
