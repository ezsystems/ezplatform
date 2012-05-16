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
    public function indexAction()
    {
        return $this->render(
            "<h1>{{ title }}</h1><h2>{{ subtitle }}</h2>",
            array(
                 "title" => "eZ Publish 5",
                 "subtitle" => "Welcome to the future !"
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
}
