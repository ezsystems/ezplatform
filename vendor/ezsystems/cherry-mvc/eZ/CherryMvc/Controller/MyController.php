<?php
/**
 * File containing the MyController class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\CherryMvc\Controller;

use eZ\CherryMvc\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MyController extends Controller
{
    public function testAction()
    {
        return $this->pageLayoutAction(
            "Test loading content",
            $this->container->get( 'ezpublish.templating' )->render(
                "content_test.html",
                array(
                    "content" => $this->repository->getContentService()->loadContent( 1 )
                )
            )
        );
    }

    public function testWithLegacyAction()
    {
        return $this->pageLayoutAction(
            "Test with Legacy template",
            $this->container->get( 'ezpublish.templating' )->render(
                "legacy_test.html",
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
            )
        );
    }

    public function helloAction( $name )
    {
        $response = new Response( "Hello $name!" );
        $response->headers->set( 'Content-Type', 'text/plain' );
        return $response;
    }

    public function byeAction()
    {
        $response = new Response( "Good bye!" );
        $response->headers->set( 'Content-Type', 'text/plain' );
        return $response;
    }

    public function helloWorldAction()
    {
        $response = new Response( "Hello World!" );
        return $response;
    }

    public function helloWorldTwigAction()
    {
        return $this->render( "hello_world.html" );
    }

    public function pageLayoutAction( $title, $content )
    {
        return $this->render(
            "pagelayout.html",
            array(
                "title" => $title,
                "content" => $content,
            )
        );
    }
}
