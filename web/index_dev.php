<?php

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.

$remoteAddresses = array( '127.0.0.1', 'fe80::1', '::1' );
if ( getenv( "DEV_REMOTE_ADDR" ) )
{
    $remoteAddresses[] = getenv( "DEV_REMOTE_ADDR" );
}

if (
    isset( $_SERVER['HTTP_CLIENT_IP'] ) ||
    isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ||
    ( isset( $_SERVER['REMOTE_ADDR'] ) && !in_array( $_SERVER['REMOTE_ADDR'], $remoteAddresses, true ) )
)
{
    header( 'HTTP/1.0 403 Forbidden' );
    exit( 'You are not allowed to access this file. Check ' . basename(__FILE__) . ' for more information.' );
}

putenv( "ENVIRONMENT=dev" );
require "index.php";
