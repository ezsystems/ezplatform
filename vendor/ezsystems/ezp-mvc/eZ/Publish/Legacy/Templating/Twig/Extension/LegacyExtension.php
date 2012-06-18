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
use eZ\Publish\Legacy\Kernel as LegacyKernel;
use eZ\Publish\Legacy\Templating\LegacyCompatible;
use eZ\Publish\Legacy\Templating\Converter\MultipleObjectConverter;
use eZTemplate;
use Twig_Extension;

/**
 * Twig extension for eZ Publish legacy
 */
class LegacyExtension extends Twig_Extension
{
    /**
     * Closure encapsulating the legacy kernel
     *
     * @var \Closure
     */
    private $legacyKernelClosure;

    /**
     * @var \eZ\Publish\Legacy\Templating\Converter\MultipleObjectConverter
     */
    private $objectConverter;

    public function __construct( \Closure $legacyKernelClosure, MultipleObjectConverter $objectConverter )
    {
        $this->legacyKernelClosure = $legacyKernelClosure;
        $this->objectConverter = $objectConverter;
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
        $objectConverter = $this->objectConverter;
        return $this->getLegacyKernel()->runCallback(
            function() use ( $tplPath, $params, $objectConverter )
            {
                $tpl = eZTemplate::factory();
                foreach ( $params as $varName => $param )
                {
                    if ( !is_object( $param ) || $param instanceof LegacyCompatible )
                    {
                        $tpl->setVariable( $varName, $param );
                    }
                    else
                    {
                        $objectConverter->register( $param, $varName );
                    }
                }

                // Get converted objects if any and pass them to the template
                foreach ( $objectConverter->convertAll() as $varName => $obj )
                {
                    $tpl->setVariable( $varName, $obj );
                }

                return $tpl->fetch( $tplPath );
            }
        );
    }

    /**
     * Returns the legacy kernel object.
     *
     * @return \eZ\Publish\Legacy\Kernel
     */
    final protected function getLegacyKernel()
    {
        $closure = $this->legacyKernelClosure;
        return $closure();
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
