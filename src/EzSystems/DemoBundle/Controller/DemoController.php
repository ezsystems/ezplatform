<?php
/**
 * File containing the MyController class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\DemoBundle\Controller;

use eZ\Publish\MVC\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoController extends Controller
{
    public function testAction( $contentId )
    {
        return $this->render(
            "eZDemoBundle:content:content_test.html.twig",
            array(
                "content" => $this->getRepository()->getContentService()->loadContent( $contentId )
            )
        );
    }

    public function testWithLegacyAction()
    {
        return $this->render(
            "eZDemoBundle:content:legacy_test.html.twig",
            array(
                "title" => "eZ Publish 5",
                "subtitle" => "Welcome to the future !",
                "legacyTemplateResult" => $this->container->get( 'ezpublish_legacy.template_bridge' )->renderTemplate(
                    "design:test/helloworld.tpl",
                    array(
                         'message' => 'All your eZ Publish base are belong to us ;-)',
                         'konamiCode' => array( 'Up', 'Up', 'Down', 'Down', 'Left', 'Right', 'Left', 'Right', 'B', 'A' )
                    )
                )
            )
        );
    }

    public function helloWorldAction()
    {
        $response = new Response( "Hello World!" );
        return $response;
    }

    public function helloWorldTwigAction()
    {
        return $this->render( "eZDemoBundle::hello_world.html.twig" );
    }

    public function editorialAction( $contentId )
    {
        return $this->render(
            "eZDemoBundle:content:editorial.html.twig",
            array(
                 "content" => $this->getRepository()->getContentService()->loadContent( $contentId )
            )
        );
        return $this->pageLayoutAction( "Editorial Interface", "<p>HERE GOES THE OBJECT</p>" );
    }
}
