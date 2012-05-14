<?php
/**
 * File containing the Resolver class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\CherryMvc\Controller;

use eZ\CherryMvc\Template\Factory as TemplateFactory;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

/**
 * eZ Publish specific Controller Resolver
 */
class Resolver extends ControllerResolver
{
    /**
     * Template engine factory
     *
     * @var \eZ\CherryMvc\Template\Factory
     */
    protected $templateEngineFactory;

    /**
     * Constructor.
     *
     * @param \eZ\CherryMvc\Template\Factory $templateEngineFactory
     * @param LoggerInterface $logger A LoggerInterface instance
     */
    public function __construct( TemplateFactory $templateEngineFactory, LoggerInterface $logger = null )
    {
        parent::__construct( $logger );
        $this->templateEngineFactory = $templateEngineFactory;
    }

    /**
     * Returns a callable for the given controller.
     *
     * @param string $controller A Controller string
     *
     * @return mixed A PHP callable
     */
    protected function createController($controller)
    {
        $return = parent::createController( $controller );
        $return[0]->setTemplateEngineFactory( $this->templateEngineFactory );
        return $return;
    }
}
