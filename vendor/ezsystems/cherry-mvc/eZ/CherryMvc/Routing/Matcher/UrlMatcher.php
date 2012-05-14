<?php
/**
 * File containing the UrlMatcher class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\CherryMvc\Routing\Matcher;

use eZ\CherryMvc\Event\EventDispatcherAwareInterface;
use eZ\CherryMvc\Event\UrlAliasMatcherEvent;
use eZ\CherryMvc\Event\FallbackMatcherEvent;
use eZ\CherryMvc\Event\CherryMvcEvents;
use Symfony\Component\Routing\Matcher\UrlMatcher as BaseUrlMatcher;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UrlMatcher extends BaseUrlMatcher implements EventDispatcherAwareInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    public function __construct( RouteCollection $routes, RequestContext $context, EventDispatcherInterface $dispatcher )
    {
        parent::__construct($routes, $context);
        $this->dispatcher = $dispatcher;
    }

    /**
     * Gets the dispatcher
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Sets the dispatcher
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function setDispatcher( EventDispatcherInterface $dispatcher )
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * 1. Tries to match $pathinfo against defined routes
     * 2. If an UrlAliasMatcher is defined, tries to match an Url alias with it
     * 3. If a FallbackMatcher is defined, tries to match with it.
     *
     * @param string $pathinfo The URI to match against
     * @return array|null
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function match( $pathinfo )
    {
        try
        {
            return parent::match( $pathinfo );
        }
        catch ( ResourceNotFoundException $e )
        {
            // Send event for UrlAlias matching
            $urlAliasEvent = new UrlAliasMatcherEvent( $this->context );
            $this->dispatcher->dispatch( CherryMvcEvents::URL_ALIAS_MATCH, $urlAliasEvent );
            if ( $urlAliasEvent->hasMatcher() )
            {
                $parameters = $urlAliasEvent->getMatcher()->match( $pathinfo );
                if ( $parameters !== null )
                    return $parameters;
            }
            unset( $urlAliasEvent );

            // Last chance to get routing parameters : FallbackMatcherEvent
            $fallbackEvent = new FallbackMatcherEvent( $this->context );
            $this->dispatcher->dispatch( CherryMvcEvents::FALLBACK, $fallbackEvent );
            if ( $fallbackEvent->hasMatcher() )
            {
                $parameters = $fallbackEvent->getMatcher()->match( $pathinfo );
                if ( $parameters !== null )
                    return $parameters;
            }
            unset( $fallbackEvent );

            // Re-throw the caught exception in nothing has been matched
            throw $e;
        }
    }
}
