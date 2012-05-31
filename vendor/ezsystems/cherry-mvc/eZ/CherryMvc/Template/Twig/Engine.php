<?php
/**
 * File containing the Twig template Engine class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\CherryMvc\Template\Twig;

use Symfony\Component\Templating\EngineInterface;
use Twig_Environment;

class Engine implements EngineInterface
{
    /**
     * @var \Twig_Environment
     */
    private $environment;

    public function __construct( Twig_Environment $environment )
    {
        $this->environment = $environment;
    }

    /**
     * Renders a template.
     *
     * @param mixed $name       A template name or a TemplateReferenceInterface instance
     * @param array $parameters An array of parameters to pass to the template
     *
     * @return string The evaluated template as a string
     *
     * @throws \RuntimeException if the template cannot be rendered
     *
     * @api
     */
    public function render( $name, array $parameters = array() )
    {
        return $this->environment->loadTemplate( $name )->render( $parameters );
    }

    /**
     * Returns true if the template exists.
     *
     * @param mixed $name A template name or a TemplateReferenceInterface instance
     *
     * @return Boolean true if the template exists, false otherwise
     *
     * @api
     */
    public function exists( $name )
    {
        try
        {
            $this->environment->loadTemplate( $name );
        }
        catch ( \Twig_Error_Loader $e )
        {
            return false;
        }

        return true;
    }

    /**
     * Returns true if this class is able to render the given template.
     *
     * @param mixed $name A template name or a TemplateReferenceInterface instance
     *
     * @return Boolean true if this class supports the given template, false otherwise
     *
     * @api
     */
    public function supports( $name )
    {
        // TODO: Properly check support
        return true;
    }
}
