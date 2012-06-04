<?php
/**
 * File containing the Controller class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\MVC;

use eZ\Publish\MVC\Template\Factory as TemplateFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\API\Repository\Repository;

class Controller extends BaseController
{
    /**
     * @return \eZ\Publish\API\Repository\Repository
     */
    public function getRepository()
    {
        if ( !$this->container->has( 'ezpublish.api.repository' ) )
            throw new \LogicException( 'The EzPublishCoreBundle has not been registered in your application.' );

        return $this->container->get( 'ezpublish.api.repository' );
    }
}
