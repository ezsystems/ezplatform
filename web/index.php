<?php
require_once __DIR__.'/../vendor/autoload.php';

use eZ\CherryMvc\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;

$routes = include __DIR__.'/../src/routes.php';

// Routes will be handled by the RouterListener, which interacts with kernel.request event
$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(
    new HttpKernel\EventListener\RouterListener(
        new Routing\Matcher\UrlMatcher(
            $routes,
            new Routing\RequestContext()
        )
    )
);

$kernel = new Kernel(
    $dispatcher,
    new HttpKernel\Controller\ControllerResolver()
);

$response = $kernel->handle( Request::createFromGlobals() );
$response->send();
