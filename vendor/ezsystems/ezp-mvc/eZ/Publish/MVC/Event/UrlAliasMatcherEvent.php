<?php
/**
 * File containing the UrlAliasMatcherEvent class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\MVC\Event;

use eZ\Publish\MVC\Routing\Matcher\UrlAliasMatcherInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Routing\RequestContext;

/**
 * This event is triggered when trying to match an URI from a Request object.
 * A listener of this event may set an URL Alias matcher object.
 */
class UrlAliasMatcherEvent extends UrlMatcherEvent
{
    /**
     * @var \eZ\Publish\MVC\Routing\Matcher\UrlAliasMatcherInterface
     */
    private $urlAliasMatcher;

    /**
     * @return \eZ\Publish\MVC\Routing\Matcher\UrlAliasMatcherInterface
     */
    public function getMatcher()
    {
        return $this->urlAliasMatcher;
    }

    /**
     * Sets the Url alias matcher object
     * @param \eZ\Publish\MVC\Routing\Matcher\UrlAliasMatcherInterface $urlAliasMatcher
     */
    public function setMatcher( UrlAliasMatcherInterface $urlAliasMatcher )
    {
        $this->urlAliasMatcher = $urlAliasMatcher;
        $this->stopPropagation();
    }

    /**
     * Returns true if an UrlAliasMatcher has been provided
     *
     * @return bool
     */
    public function hasMatcher()
    {
        return isset( $this->urlAliasMatcher );
    }
}
