<?php
/**
 * File containing the FallbackMatcher class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishLegacyBundle\Routing\Matcher;

use eZ\CherryMvc\Event\FallbackMatcherEvent;
use eZ\CherryMvc\Event\UrlAliasMatcherEvent;
use eZ\CherryMvc\Event\CherryMvcEvents;
use Symfony\Component\Routing\Matcher\UrlMatcher as BaseUrlMatcher;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class FallbackMatcher extends BaseUrlMatcher implements UrlMatcherInterface
{
    public function __construct()
    {
    }

    /**
     * Matches the LegacyController in all cases
     *
     * @param string $pathinfo The URI to match against
     *
     * @return array
     */
    public function match( $pathinfo )
    {
        return array(
            "_route" => "eZLegacyFallback",
            "_controller" => "eZ\\Bundle\\EzPublishLegacyBundle\\Controller\\LegacyKernelController::indexAction",
        );
    }
}
