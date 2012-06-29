<?php
require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/EzPublishKernel.php';
require_once __DIR__ . '/../app/EzPublishCache.php';

use eZ\Publish\MVC\SiteAccess\Router as SiteAccessRouter;
use Symfony\Component\HttpFoundation\Request;

$kernel = new EzPublishKernel( 'dev', true );
$kernelCache = new EzPublishCache( $kernel );
$kernelCache->handle( Request::createFromGlobals() )->send();;
