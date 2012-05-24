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
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * eZ Publish specific Controller Resolver
 */
class Resolver extends ControllerResolver
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * Constructor.
     *
     * @param \eZ\CherryMvc\Template\Factory $templateEngineFactory
     * @param \Symfony\Component\HttpKernel\Log\LoggerInterface $logger A LoggerInterface instance
     */
    public function __construct( ContainerInterface $container, LoggerInterface $logger = null )
    {
        $this->container = $container;
        parent::__construct( $logger );
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
        // $callbackController should be an array
        // [0] is the controller object
        // [1] is the controller method to call
        $callbackController = parent::createController( $controller );
        $controller = $callbackController[0];
        if ( $controller instanceof ContainerAwareInterface )
            $controller->setContainer( $this->container );

        $controller->setRepository( $this->container->get( 'ezpublish.api.repository' ) );

        return $callbackController;
    }
}
