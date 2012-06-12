<?php
/**
 * File containing the LegacyExtension class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Legacy\Templating\Twig\Extension;

use eZ\Publish\Legacy\Templating\Twig\TokenParser\LegacyIncludeParser;
use eZ\Bundle\EzPublishLegacyBundle\Services\LegacyKernel;
use eZTemplate;

/**
 * Twig extension for eZ Publish legacy
 */
class LegacyExtension extends \Twig_Extension
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
     * @param string $tplPath Path to template (i.e. "design:setup/info.tpl")
     * @param array $params Parameters to pass to template.
     *                      Consists of a hash with key as the variable name available in the template.
     * @return string The legacy template result
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

    public function getTokenParsers()
    {
        return array(
            new LegacyIncludeParser()
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'ezpublish.legacy';
    }
}
