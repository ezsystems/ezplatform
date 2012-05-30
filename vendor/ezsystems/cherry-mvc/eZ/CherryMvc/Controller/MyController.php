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
        $contentService = $this->repository->getContentService();

        return $this->render(
            "<h1>{{ content.fields['name']['eng-GB'] }}</h1><h2>{{ content.fields['short_name']['eng-GB'] }}</h2><p>{{ content.fields['description']['eng-GB']|nl2br }}</p>",
            array(
                 "content"      => $contentService->loadContent( 1 )
            )
        );
    }

    public function testWithLegacyAction()
    {
        $templateBridge = $this->container->get( 'ezpublish_legacy.template_bridge' );
        return $this->render(
            "<h1>{{ title }}</h1><h2>{{ subtitle }}</h2>{{ legacyTemplateResult|raw }}",
            array(
                 "title" => "eZ Publish 5",
                 "subtitle" => "Welcome to the future !",
                 "legacyTemplateResult" => $templateBridge->renderTemplate(
                     'design:test/helloworld.tpl',
                     array(
                          'message' => 'All your eZ Publish base are belong to us ;-)',
                          'konamiCode' => array( 'Up', 'Up', 'Down', 'Down', 'Left', 'Right', 'Left', 'Right', 'B', 'A' )
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
        return $this->render(
            "Hello World!"
        );
    }
}
