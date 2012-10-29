<?php

use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../ezpublish/autoload.php';
require_once __DIR__ . '/../ezpublish/EzPublishKernel.php';
require_once __DIR__ . '/../ezpublish/EzPublishCache.php';

$kernel = new EzPublishKernel( 'dev', true );
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle( $request );
$response->send();
$kernel->terminate( $request, $response );
