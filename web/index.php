<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ClassLoader\ApcClassLoader;

$loader = require_once __DIR__ . '/../ezpublish/autoload.php';

// Use APC for autoloading to improve performance:
// Change 'ezpublish' to a unique prefix in order to prevent cache key conflicts
// with other applications also using APC.
/*
$loader = new ApcClassLoader( 'ezpublish', $loader );
$loader->register( true );
*/

require_once __DIR__ . '/../ezpublish/EzPublishKernel.php';
require_once __DIR__ . '/../ezpublish/EzPublishCache.php';

$kernel = new EzPublishKernel( 'prod', false );
$kernel->loadClassCache();
// Comment the following line if you use an external reverse proxy (e.g. Varnish)
$kernel = new EzPublishCache( $kernel );
$request = Request::createFromGlobals();
// Uncomment the following if your application is behind a reverse proxy you manage and trust.
// (see http://fabien.potencier.org/article/51/create-your-own-framework-on-top-of-the-symfony2-components-part-2)
//Request::trustProxyData();
$response = $kernel->handle( $request );
$response->send();
$kernel->terminate( $request, $response );
