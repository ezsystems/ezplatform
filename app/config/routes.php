<?php
use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();
$routes->add(
    'test',
    new Routing\Route(
        '/test/{contentId}',
        array(
             'contentId' => 1,
             '_controller' => 'eZ\\CherryMvc\\Controller\\MyController::testAction'
        )
    )
);
$routes->add(
    'testWithLegacy',
    new Routing\Route(
        '/test/legacy',
        array(
             '_controller' => 'eZ\\CherryMvc\\Controller\\MyController::testWithLegacyAction'
        )
    )
);
$routes->add(
    'hello',
    new Routing\Route(
        '/hello/{name}',
        array(
             'name' => 'World',
             '_controller' => 'eZ\\CherryMvc\\Controller\\MyController::helloAction'
        )
    )
);
$routes->add(
    'bye',
    new Routing\Route(
        '/bye',
        array(
             '_controller' => 'eZ\\CherryMvc\\Controller\\MyController::byeAction'
        )
    )
);
$routes->add(
    'setupInfo',
    new Routing\Route(
        '/ezdemo_site_admin/setup/info',
        array(
             '_controller' => 'eZ\\Bundle\\EzPublishLegacyBundle\\Controller\\SetupController::infoAction'
        )
    )
);
$routes->add(
    'helloWorld',
    new Routing\Route(
        '/helloWorld',
        array(
             '_controller' => 'eZ\\CherryMvc\\Controller\\MyController::helloWorldAction'
        )
    )
);
$routes->add(
    'helloWorldTwig',
    new Routing\Route(
        '/helloWorldTwig',
        array(
             '_controller' => 'eZ\\CherryMvc\\Controller\\MyController::helloWorldTwigAction'
        )
    )
);
$routes->add(
    'editorial',
    new Routing\Route(
        '/Editorial',
        array(
             '_controller' => 'eZ\\CherryMvc\\Controller\\MyController::editorialAction'
        )
    )
);
return $routes;
