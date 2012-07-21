<?php
/**
 * File containing the MyController class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\DemoBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \DateTime;

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

    public function testWithLegacyAction( $contentId )
    {
        return $this->render(
            "eZDemoBundle:content:legacy_test.html.twig",
            array(
                "title" => "eZ Publish 5",
                "subtitle" => "Welcome to the future !",
                "messageForLegacy" => "All your eZ Publish base are belong to us ;-)",
                "content"      => $this->getRepository()->getContentService()->loadContent( $contentId )
            )
        );
    }

    public function helloWorldAction()
    {
        $response = new Response( "Hello World!" );
        return $response;
    }

    public function helloWorldCachedAction()
    {
        $response = new Response();
        $response->setPublic();
        $response->setETag( "HelloWorldTag" );
        $response->setLastModified( new DateTime( "2012-01-01 00:00:00+0000" ) );

        // Check that the Response is not modified for the given Request
        if ( $response->isNotModified( $this->getRequest() ) )
        {
            // return the 304 Response immediately
            return $response;
        }
        $response->setContent( "Hello Universe!" );

        return $response;
    }

    public function helloWorldTwigAction()
    {
        return $this->render( "eZDemoBundle::hello_world.html.twig" );
    }

    public function helloWorldTwigCachedAction()
    {
        $response = new Response();
        $response->setPublic();
        $response->setETag( "HelloWorldTwigTag" );
//        $response->setLastModified( new DateTime( "2012-01-01 00:00:00+0000" ) );

        // Check that the Response is not modified for the given Request
        if ( $response->isNotModified( $this->getRequest() ) )
        {
            // return the 304 Response immediately
            return $response;
        }

        return $this->render( "eZDemoBundle::hello_world.html.twig", array(), $response );
    }


    public function editorialAction( $contentId )
    {
        return $this->render(
            "eZDemoBundle:content:editorial.html.twig",
            array(
                 "content" => $this->getRepository()->getContentService()->loadContent( $contentId )
            )
        );
    }
}
