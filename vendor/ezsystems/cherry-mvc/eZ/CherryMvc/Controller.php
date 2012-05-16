<?php
/**
 * File containing the Controller class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\CherryMvc;

use eZ\CherryMvc\Template\Factory as TemplateFactory;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;

class Controller extends ContainerAware
{
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
}
