<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ClassLoader\ApcClassLoader;

$loader = require_once __DIR__ . '/../ezpublish/autoload.php';


// Use APC for autoloading to improve performance:
// Change 'ezpublish' to a unique prefix in order to prevent cache key conflicts
// with other applications also using APC.
//
// ( Not needed when using `php composer.phar dump-autoload --optimize` )
/*
$loader = new ApcClassLoader('ezpublish', $loader);
$loader->register(true);
*/

require_once __DIR__ . '/../ezpublish/EzPublishKernel.php';
require_once __DIR__ . '/../ezpublish/EzPublishCache.php';

$kernel = new EzPublishKernel( 'prod', false );
$kernel->loadClassCache();
$kernel = new EzPublishCache( $kernel );
$request = Request::createFromGlobals();
// Uncomment the following if your application is behind a reverse proxy you manage and trust.
//Request::trustProxyData();
$response = $kernel->handle( $request );
$response->send();
$kernel->terminate( $request, $response );
