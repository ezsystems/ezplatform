<?php
/**
 * File containing the ChainRouter class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\MVC\Routing;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * The ChainRouter is an aggregation of valid routers and allows URL matching against multiple routers.
 * This can be particularly useful in the case of static + dynamic routes (i.e. that need to be fetched from a content repository)
 */
class ChainRouter implements RouterInterface, WarmableInterface
{
    /**
     * @var \Symfony\Component\Routing\RequestContext
     */
    protected $context;

    /**
     * @var array Array indexed by priority.
     *            Each priority key is an array of routers having this priority.
     *            The highest priority number is the highest priority
     */
    protected $routers = array();

    /**
     * @var \Symfony\Component\Routing\RouterInterface[] Array of routers, sorted by priority
     */
    protected $sortedRouters;

    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    protected $routeCollection;

    public function __construct( RequestContext $context = null )
    {
        $this->context = $context ?: new RequestContext();
    }

    /**
     * Registers $router as a valid router to be used in the routing chain.
     * When this router will be called in the chain depends on $priority. The highest $priority is, the earliest the router will be called.
     *
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param int $priority
     */
    public function addRouter( RouterInterface $router, $priority = 0 )
    {
        $priority = (int)$priority;
        if ( !isset( $this->routers[$priority] ) )
            $this->routers[$priority] = array();

        $this->routers[$priority][] = $router;
    }

    /**
     * @return \Symfony\Component\Routing\RouterInterface[]
     */
    public function getAllRouters()
    {
        if ( empty( $this->sortedRouters ) )
            $this->sortedRouters = $this->sortRouters();

        return $this->sortedRouters;
    }

    /**
     * Sort the registered routers by priority.
     * The highest priority number is the highest priority (reverse sorting)
     *
     * @return \Symfony\Component\Routing\RouterInterface[]
     */
    private function sortRouters()
    {
        $sortedRouters = array();
        krsort( $this->routers );

        foreach ( $this->routers as $routers )
        {
            $sortedRouters = array_merge( $sortedRouters, $routers );
        }

        return $sortedRouters;
    }

    /**
     * Gets the RouteCollection instance associated with this Router.
     * This collection is an aggregation of all routers' inner RouterCollection instance.
     *
     * @return \Symfony\Component\Routing\RouteCollection A RouteCollection instance
     */
    public function getRouteCollection()
    {
        if ( !$this->routeCollection instanceof RouteCollection )
        {
            $this->routeCollection = new RouteCollection();
            foreach ( $this->getAllRouters() as $router )
            {
                $this->routeCollection->addCollection( $router->getRouteCollection() );
            }
        }

        return $this->routeCollection;
    }

    /**
     * Loop against registered routers and tries to match $pathinfo
     *
     * @param string $pathinfo
     * @return array|void
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MethodNotAllowedException
     */
    public function match( $pathinfo )
    {
        $httpMethodMismatch = null;

        foreach ( $this->getAllRouters() as $router )
        {
            try
            {
                return $router->match( $pathinfo );
            }
            catch ( ResourceNotFoundException $e )
            {
                // Do nothing, just let the next router handle it.
            }
            catch ( MethodNotAllowedException $e )
            {
                // MethodNotAllowedException is a bit more specific, since the route has been matched, but HTTP method hasn't
                // So we keep it in case no other router is able to match the same route with this method.
                $httpMethodMismatch = $e;
            }
        }

        // Finally throw a ResourceNotFoundException since the chain router couldn't find any valid router for current route
        throw $httpMethodMismatch ?: new ResourceNotFoundException( "Couldn't find any router able to match '$pathinfo'" );
    }

    /**
     * Loops against all registered routers and try to generate an URL from a route name.
     *
     * @param string $name The route's name we want to generate URL from
     * @param array $parameters
     * @param bool $absolute
     * @return string
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function generate( $name, $parameters = array(), $absolute = false )
    {
        foreach ( $this->getAllRouters() as $router )
        {
            try
            {
                return $router->generate( $name, $parameters, $absolute );
            }
            catch ( RouteNotFoundException $e )
            {
                // Do nothing, hust let the next router handle it
            }
        }

        throw new RouteNotFoundException( "None of the registered routers was able to generate route '$name'" );
    }

    /**
     * Registers current request context
     *
     * @param \Symfony\Component\Routing\RequestContext $context
     */
    public function setContext( RequestContext $context )
    {
        $this->context = $context;

        foreach ( $this->getAllRouters() as $router )
        {
            $router->setContext( $context );
        }
    }

    /**
     * @return \Symfony\Component\Routing\RequestContext
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Warms up the cache for each router if it supports warmup mechanism.
     *
     * @param string $cacheDir The cache directory
     */
    public function warmUp( $cacheDir )
    {
        foreach ( $this->getAllRouters() as $router )
        {
            if ( $router instanceof WarmableInterface )
            {
                $router->warmup( $cacheDir );
            }
        }
    }
}
