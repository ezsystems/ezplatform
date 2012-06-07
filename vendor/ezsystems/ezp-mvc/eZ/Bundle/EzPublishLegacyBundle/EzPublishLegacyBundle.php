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
        if ( !$this->container->getParameter( 'ezpublish_legacy.enabled' ) )
            return;

        require_once $this->container->getParameter( 'ezpublish_legacy.root_dir' ) . "/autoload.php";
    }

}
