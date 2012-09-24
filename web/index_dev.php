<?php

use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/EzPublishKernel.php';
require_once __DIR__ . '/../app/EzPublishCache.php';

$kernel = new EzPublishKernel( 'dev', true );
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle( $request );
$response->send();
$kernel->terminate( $request, $response );
