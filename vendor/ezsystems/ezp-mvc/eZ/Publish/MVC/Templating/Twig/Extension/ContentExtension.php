<?php
/**
 * File containing the ContentExtension class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\MVC\Templating\Twig\Extension;

use \Twig_Extension;
use \Twig_Environment;
use eZ\Publish\MVC\Templating\Twig\Helper\ContentHelper;

/**
 * Twig content extension for eZ Publish specific usage.
 * Exposes helpers to play with public API objects.
 */
class ContentExtension extends Twig_Extension
{
    /**
     * @var \eZ\Publish\MVC\Templating\Twig\Helper\ContentHelper
     */
    protected $contentHelper;

    public function __construct( ContentHelper $contentHelper )
    {
        $this->contentHelper = $contentHelper;
    }

    /**
     * Initializes the template runtime (aka Twig environment).
     *
     * @param \Twig_Environment $environment
     */
    public function initRuntime( Twig_Environment $environment )
    {
        parent::initRuntime( $environment );
        $this->contentHelper->setTwigEnvironment( $environment );
    }

    /**
     * Global variables that are exposed to all templates.
     *
     * @return array
     */
    public function getGlobals()
    {
        return array(
            'ezpublish'     => $this->contentHelper
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'ezpublish.content';
    }
}
