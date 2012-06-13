<?php
/**
 * File containing the Resolver class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\MVC\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver;

/**
 * eZ Publish specific Controller Resolver
 */
class Resolver extends ControllerResolver
{
    /**
     * Returns a callable for the given controller.
     *
     * @param string $controller A Controller string
     *
     * @return mixed A PHP callable
     */
    protected function createController( $controller )
    {
        // TODO: Is it still needed ?
        // $callbackController should be an array
        // [0] is the controller object
        // [1] is the controller method to call
        $callbackController = parent::createController( $controller );
        $controller = $callbackController[0];
        $controller->setRepository( $this->container->get( 'ezpublish.api.repository' ) );

        return $callbackController;
    }
}
