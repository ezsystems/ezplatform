<?php
/**
 * File containing the EzPublishLegacy class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishLegacyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use eZ\Bundle\EzPublishLegacyBundle\Event\Listener\Fallback as FallbackListener;
use eZ\Bundle\EzPublishLegacyBundle\Routing\Matcher\FallbackMatcher;

class EzPublishLegacyBundle extends Bundle
{
    public function boot()
    {
        // TODO: Would be better to use service tags instead of the event system for this
        $dispatcher = $this->container->get( 'event_dispatcher' );
        $dispatcher->addSubscriber(
            new FallbackListener(
                new FallbackMatcher()
            )
        );
    }

}
