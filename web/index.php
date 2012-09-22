<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ClassLoader\ApcClassLoader;;

$loader = require_once __DIR__ . '/../app/autoload.php';


// Use APC for autoloading to improve performance.
// Change 'sf2' to a unique prefix in order to prevent cache key conflicts
// with other applications also using APC.
/*
$loader = new ApcClassLoader('sf2', $loader);
$loader->register(true);
*/

require_once __DIR__ . '/../app/EzPublishKernel.php';
require_once __DIR__ . '/../app/EzPublishCache.php';

$kernel = new EzPublishKernel( 'prod', false );
$kernel->loadClassCache();
$kernelCache = new EzPublishCache( $kernel );
$kernelCache->handle( Request::createFromGlobals() )->send();
