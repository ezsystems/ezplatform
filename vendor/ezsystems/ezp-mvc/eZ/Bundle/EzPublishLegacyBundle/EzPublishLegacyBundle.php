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
        if ( $this->container->getParameter( 'ezpublish_legacy.enabled' ) )
        {
            // To properly register legacy autoload, we need to go to the legacy root dir
            // since legacy autoload.php has some dependencies on files called with relative paths (i.e. config.php)
            $workingDir = getcwd();
            chdir( $this->container->getParameter( 'ezpublish_legacy.root_dir' ) );
            require_once "autoload.php";
            chdir( $workingDir );

            // TODO: Would be better to use service tags instead of the event system for this
            $this->container->get( 'event_dispatcher' )->addSubscriber(
                new FallbackListener(
                    new FallbackMatcher()
                )
            );
        }
    }

}
