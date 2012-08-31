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
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
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

    /**
     * Renders the top menu, with cache control
     *
     * @param int $locationId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function topMenuAction( $locationId )
    {
        $response = new Response;
        $response->setPublic();
        $response->setMaxAge( 60 );
        $location = $this->getRepository()->getLocationService()->loadLocation( $locationId );

        return $this->render(
            "eZDemoBundle::page_topmenu.html.twig",
            array(
                "locations" => $this->getRepository()->getLocationService()->loadLocationChildren( $location )
            ),
            $response
        );
    }

    /**
     * Renders the latest content for footer, with cache control
     *
     * @param string $pathString
     * @param string $contentTypeIdentifier
     * @param int $limit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function latestContentAction( $pathString, $contentTypeIdentifier, $limit )
    {
        $response = new Response;
        $response->setPublic();
        $response->setMaxAge( 60 );

        $contentType = $this->getRepository()->getContentTypeService()->loadContentTypeByIdentifier( $contentTypeIdentifier );
        $query = new Query(
            array(
                 'criterion' => new Criterion\LogicalAnd(
                     array(
                          new Criterion\Subtree( $pathString ),
                          new Criterion\ContentTypeId( $contentType->id )
                     )
                 ),
                 'sortClauses' => array(
                     new SortClause\DatePublished( Query::SORT_DESC )
                 )
            )
        );
        $query->limit = $limit;

        return $this->render(
            "eZDemoBundle:footer:latest_content.html.twig",
            array(
                "latestContent" => $this->getRepository()->getSearchService()->findContent( $query )
            ),
            $response
        );
    }

    public function footerAction( $contentTypeIdentifier )
    {
        $response = new Response;
        $response->setPublic();
        $response->setMaxAge( 60 );
        $contentType = $this->getRepository()->getContentTypeService()->loadContentTypeByIdentifier( $contentTypeIdentifier );

        $query = new Query(
            array(
                 'criterion' => new Criterion\LogicalAnd(
                     array(
                          new Criterion\Subtree( '/1/2/' ),
                          new Criterion\ContentTypeId( $contentType->id )
                     )
                 ),
                 'sortClauses' => array(
                     new SortClause\DatePublished( Query::SORT_DESC )
                 )
            )
        );
        $query->limit = 1;

        $searchResult = $this->getRepository()->getSearchService()->findContent( $query );
        $content = isset( $searchResult->searchHits[0] ) ? $searchResult->searchHits[0]->valueObject  : null;

        return $this->render(
            "eZDemoBundle::page_footer.html.twig",
            array(
                "content" => $content
            ),
            $response
        );
    }
}
