<?php
use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();
$routes->add(
    'index',
    new Routing\Route(
        '/',
        array(
             '_controller' => 'eZ\\CherryMvc\\Controller\\MyController::indexAction'
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
return $routes;
