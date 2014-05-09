<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

/**
 * Router script for running eZ Publish CMS on top of PHP 5.4 built-in webserver
 * WARNING !!! Use it for DEVELOPMENT purpose ONLY !!!
 * This script is provided as is, use it at your own risk !
 */
if ( !isset( $_SERVER['SERVER_PROTOCOL'] ) )
{
    echo <<<EOT
This is a router script to be used with built-in PHP server (available as of PHP 5.4.0).
It will set up all needed rewrite rules for eZ Publish.
 
Usage
-----
From your command line, type :
 
    $ cd /path/to/ezpublish5/folder
    $ php ezpublish/console server:run -r ../bin/ezrouter.php localhost:8000
 
This will start PHP webserver for localhost on port 8000.
You can of course replace localhost by another host. Port is also customizable.
 
For more information on PHP webserver, see http://php.net/manual/en/features.commandline.webserver.php
 
EOT;
    exit;
}

// Use indev_dev.php front controller to have all debug tools and info unless ENVIRONMENT is set.
if ( getenv( "ENVIRONMENT" ) === false )
    $script = 'index_dev.php';
else
    $script = 'index.php';

// To stick with regular Apache HTTPD behaviour, SCRIPT_NAME should equal to PHP_SELF.
// Fix SCRIPT_NAME and PHP_SELF since we deal with virtual folders, so PHP server would append /index.php to it.
$_SERVER['SCRIPT_NAME'] = $_SERVER['PHP_SELF'] = str_replace( '/index.php', '', $_SERVER['SCRIPT_NAME'] );

// If requested resource exists, we serve it directly.
if ( is_file( $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $_SERVER['SCRIPT_NAME'] ) )
{
    return false;
}

// REST API v1, you might want to adapt this pattern if you use custom legacy REST API.
if ( strpos( $_SERVER['REQUEST_URI'], '/api/ezp/v1' ) === 0 )
{
    $script = 'index_rest.php';
}

// Setup some missing $_SERVER vars that are needed by legacy kernel.
if ( !isset( $_SERVER['SERVER_ADDR'] ) )
    $_SERVER['SERVER_ADDR'] = gethostbyname( $_SERVER['SERVER_NAME'] );
if ( !isset( $_SERVER['QUERY_STRING'] ) )
    $_SERVER['QUERY_STRING'] = '';
if ( !isset( $_SERVER['CONTENT_LENGTH'] ) )
    $_SERVER['CONTENT_LENGTH'] = '';

$_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $script;
require $script;
