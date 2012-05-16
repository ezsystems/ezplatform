<?php
/**
 * File containing the Fallback class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishLegacyBundle\Event\Listener;

use eZ\CherryMvc\Event\CherryMvcEvents;
use eZ\CherryMvc\Event\FallbackMatcherEvent;
use eZ\Bundle\EzPublishLegacyBundle\Routing\Matcher\FallbackMatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Fallback implements EventSubscriberInterface
{
    public function __construct( FallbackMatcher $fallbackMatcher )
    {
        $this->fallbackMatcher = $fallbackMatcher;
    }

    public function onFallbackRequest( FallbackMatcherEvent $event )
    {
        $event->setMatcher( $this->fallbackMatcher );
    }

    static function getSubscribedEvents()
    {
        return array(
            CherryMvcEvents::FALLBACK => "onFallbackRequest",
        );
    }
}
