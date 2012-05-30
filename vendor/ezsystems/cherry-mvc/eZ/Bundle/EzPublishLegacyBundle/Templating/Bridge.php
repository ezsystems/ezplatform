<?php
/**
 * File containing the legacy template Bridge class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishLegacyBundle\Templating;
use eZ\Bundle\EzPublishLegacyBundle\Services\LegacyKernel;
use eZTemplate;

class Bridge
{
    /**
     * @var \eZ\Bundle\EzPublishLegacyBundle\Services\LegacyKernel
     */
    private $legacyKernel;

    public function __construct( LegacyKernel $legacyKernel )
    {
        $this->legacyKernel = $legacyKernel;
    }

    /**
     * Renders a legacy template.
     *
     * @param $tplPath Path to template (i.e. "design:setup/info.tpl")
     * @param array $params Parameters to pass to template.
     *                      Consists of a hash with key as the variable name available in the template.
     */
    public function renderTemplate( $tplPath, array $params = array() )
    {
        $tplResult = $this->legacyKernel->runCallback(
            function() use ( $tplPath, $params )
            {
                $tpl = eZTemplate::factory();
                foreach ( $params as $varName => $param )
                {
                    $tpl->setVariable( $varName, $param );
                }

                return $tpl->fetch( $tplPath );
            }
        );

        return $tplResult;
    }
}
