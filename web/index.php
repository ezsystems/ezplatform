<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__ . '/../app/EzPublishKernel.php';

use Symfony\Component\HttpFoundation\Request;

$kernel = new EzPublishKernel( 'dev', true );
$kernel->loadClassCache();
$response = $kernel->handle( Request::createFromGlobals() );
$response->send();
