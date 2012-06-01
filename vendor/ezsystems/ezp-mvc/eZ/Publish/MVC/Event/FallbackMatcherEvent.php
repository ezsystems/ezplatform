<?php
/**
 * File containing the FallbackMatcherEvent class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\MVC\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

/**
 * This event is triggered when trying to match an URI from a Request object.
 * A listener of this event may set an UrlMatcherInterface implementation.
 */
class FallbackMatcherEvent extends UrlMatcherEvent
{
    /**
     * @var \Symfony\Component\Routing\Matcher\UrlMatcherInterface
     */
    private $matcher;

    /**
     * @return \eZ\Publish\MVC\Routing\Matcher\UrlAliasMatcherInterface
     */
    public function getMatcher()
    {
        return $this->matcher;
    }

    /**
     * Sets the Url alias matcher object
     * @param \Symfony\Component\Routing\Matcher\UrlMatcherInterface $matcher
     */
    public function setMatcher( UrlMatcherInterface $matcher )
    {
        $this->matcher = $matcher;
        $this->stopPropagation();
    }

    /**
     * Returns true if an UrlAliasMatcher has been provided
     *
     * @return bool
     */
    public function hasMatcher()
    {
        return isset( $this->matcher );
    }
}
