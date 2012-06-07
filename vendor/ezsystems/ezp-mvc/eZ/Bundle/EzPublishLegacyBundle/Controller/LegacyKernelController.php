<?php
/**
 * File containing the LegacyKernelController class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishLegacyBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;
use eZ\Bundle\EzPublishLegacyBundle\Services\LegacyKernel;
use \ezpModuleNotFound;
use \ezpModuleViewNotFound;
use \ezpModuleDisabled;
use \ezpModuleViewDisabled;
use \ezpAccessDenied;
use \ezpContentNotFoundException;
use \ezpLanguageNotFound;

/**
 * Controller embedding legacy kernel.
 */
class LegacyKernelController
{
    /**
     * The legacy kernel instance (eZ Publish 4)
     *
     * @var \eZ\Bundle\EzPublishLegacyBundle\Services\LegacyKernel
     */
    private $kernel;

    public function __construct( LegacyKernel $kernel, EngineInterface $templateEngine )
    {
        $this->kernel = $kernel;
        $this->templateEngine = $templateEngine;
    }

    /**
     * Renders a view and returns a Response.
     *
     * @param string $view The view name
     * @param array $parameters An array of parameters to pass to the view
     *
     * @return Response A Response instance
     */
    public function render( $view, array $parameters = array() )
    {
        $response = new Response();
        $response->setContent( $this->templateEngine->render( $view, $parameters ) );
        return $response;
    }

    /**
     * Base fallback action.
     * Will be basically used for every
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        try
        {
            $result = $this->kernel->run();
        }
        catch ( ezpModuleNotFound $e )
        {
            return $this->render(
                "EzPublishLegacyBundle:errors:module_not_found.html.twig",
                array( "moduleName" => $e->moduleName )
            )->setStatusCode( 404 );
        }
        catch ( ezpModuleViewNotFound $e )
        {
            return $this->render(
                "EzPublishLegacyBundle:errors:module_view_not_found.html.twig",
                array( "moduleName" => $e->moduleName, "viewName" => $e->viewName )
            )->setStatusCode( 404 );
        }
        catch ( ezpModuleDisabled $e )
        {
            return $this->render(
                "EzPublishLegacyBundle:errors:module_disabled.html.twig",
                array( "moduleName" => $e->moduleName )
            )->setStatusCode( 404 );
        }
        catch ( ezpModuleViewDisabled $e )
        {
            return $this->render(
                "EzPublishLegacyBundle:errors:module_view_disabled.html.twig",
                array( "moduleName" => $e->moduleName, "viewName" => $e->viewName )
            )->setStatusCode( 404 );
        }
        catch ( ezpAccessDenied $e )
        {
            return $this->render(
                "EzPublishLegacyBundle:errors:access_denied.html.twig"
            )->setStatusCode( 403 );
        }
        catch ( ezpContentNotFoundException $e )
        {
            return $this->render(
                "EzPublishLegacyBundle:errors:content_not_found.html.twig"
            )->setStatusCode( 404 );
        }
        catch ( ezpLanguageNotFound $e )
        {
            return $this->render(
                "EzPublishLegacyBundle:errors:language_not_found.html.twig"
            )->setStatusCode( 500 );
        }

        return new Response(
            $result["content"]
        );
    }
}
