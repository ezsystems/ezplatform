<?php
require_once __DIR__.'/../vendor/autoload.php';

use eZ\CherryMvc\Kernel;
use eZ\CherryMvc\Controller\Resolver;
use eZ\CherryMvc\Template\Factory as TemplateFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use \Twig_Loader_String;
use \Twig_Environment;

$routes = include __DIR__.'/../src/routes.php';

// Routes will be handled by the RouterListener, which interacts with kernel.request event
$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(
    new RouterListener(
        new Routing\Matcher\UrlMatcher(
            $routes,
            new Routing\RequestContext(),
            $dispatcher
        )
    )
);

$templateFactory = new TemplateFactory();
$templateFactory->register(
    "twig",
    function ()
    {
        return new Twig_Environment( new Twig_Loader_String() );
    }
);

$kernel = new Kernel(
    $dispatcher,
    new Resolver( $templateFactory )
);

$response = $kernel->handle( Request::createFromGlobals() );
$response->send();
