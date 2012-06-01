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
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\API\Repository\Repository;

class Controller extends ContainerAware
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

    /**
     * Renders $template with $params.
     *
     * @param string $template Template file or template content (depending on the engine used)
     * @param array $params Hash of params. Key is the variable name that will be made available in the template
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render( $template, array $params = array() )
    {
        return new Response(
            $this->container->get( 'ezpublish.templating' )->render( $template, $params )
        );
    }

    /**
     * @param \eZ\Publish\API\Repository\Repository $repository
     */
    public function setRepository( Repository $repository )
    {
        $this->repository = $repository;
    }
}
