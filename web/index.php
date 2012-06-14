<?php
require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/EzPublishKernel.php';
require_once __DIR__ . '/../app/EzPublishCache.php';

use Symfony\Component\HttpFoundation\Request;

$kernel = new EzPublishKernel( 'dev', true );
$kernel->loadClassCache();
$kernel = new EzPublishCache( $kernel );
$kernel->handle( Request::createFromGlobals() )->send();
