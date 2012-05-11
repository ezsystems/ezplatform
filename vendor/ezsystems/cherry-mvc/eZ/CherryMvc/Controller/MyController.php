<?php
/**
 * File containing the MyController class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\CherryMvc\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MyController
{
    public function indexAction()
    {
        return new Response( '<h1>eZ Publish 5</h1><h2>Welcome to the future !</h2>' );
    }

    public function helloAction($name)
    {
        $response = new Response("Hello $name!");
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }

    public function byeAction()
    {
        $response = new Response("Good bye!");
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }
}
