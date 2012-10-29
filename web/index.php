<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ClassLoader\ApcClassLoader;

$loader = require_once __DIR__ . '/../ezpublish/autoload.php';


// Use APC for autoloading to improve performance:
// Change 'ezpublish5' to a unique prefix in order to prevent cache key conflicts
// with other applications also using APC.
//
// ( Not needed when using `php composer.phar dump-autoload --optimize` )
/*
$loader = new ApcClassLoader('ezpublish5', $loader);
$loader->register(true);
*/

require_once __DIR__ . '/../ezpublish/EzPublishKernel.php';
require_once __DIR__ . '/../ezpublish/EzPublishCache.php';

$kernel = new EzPublishKernel( 'prod', false );
$kernel->loadClassCache();
$kernel = new EzPublishCache( $kernel );
$request = Request::createFromGlobals();
$response = $kernel->handle( $request );
$response->send();
$kernel->terminate( $request, $response );
