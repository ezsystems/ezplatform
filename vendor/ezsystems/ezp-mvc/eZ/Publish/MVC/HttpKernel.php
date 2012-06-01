<?php
/**
 * File containing the base Kernel class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\MVC;
use Symfony\Component\HttpKernel\HttpKernel as BaseHttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class HttpKernel extends BaseHttpKernel
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function __construct( EventDispatcherInterface $dispatcher, ContainerInterface $container, ControllerResolverInterface $controllerResolver )
    {
        parent::__construct( $dispatcher, $controllerResolver );

        $this->container = $container;
    }
}
