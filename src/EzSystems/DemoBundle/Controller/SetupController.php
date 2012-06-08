<?php
/**
 * File containing the SetupController class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\DemoBundle\Controller;

use eZ\Publish\MVC\Controller;
use Symfony\Component\HttpFoundation\Response;
use \eZTemplate;
use \eZDB;
use \eZPublishSDK;
use \eZExtension;
use \ezcSystemInfo;
use \ezcSystemInfoReaderCantScanOSException;
use \ezpKernel;

class SetupController extends Controller
{
    public function infoAction()
    {
        // Logic gathering all the information
        $loadedExtensions = get_loaded_extensions();
        $splAutoloadFunctions = spl_autoload_functions();
        $phpINI = array();

        foreach ( array( 'safe_mode', 'register_globals', 'file_uploads' ) as $iniName )
        {
            $phpINI[ $iniName ] = ini_get( $iniName ) != 0;
        }
        foreach ( array( 'open_basedir', 'post_max_size', 'memory_limit', 'max_execution_time' ) as $iniName )
        {
            $value = ini_get( $iniName );
            if ( $value !== '' )
                $phpINI[$iniName] = $value;
        }

        $webserverInfo = false;
        if ( function_exists( 'apache_get_version' ) )
        {
            $webserverInfo = array( 'name' => 'Apache',
                                    'modules' => false,
                                    'version' => apache_get_version() );
            if ( function_exists( 'apache_get_modules' ) )
                $webserverInfo['modules'] = apache_get_modules();
        }

        // Calling the old kernel with a closure
        $kernel = $this->container->get( 'ezpublish_legacy.kernel' );
        $infoOutput = $kernel->runCallback(
            function () use ( $loadedExtensions, $splAutoloadFunctions, $phpINI, $webserverInfo )
            {
                $db = eZDB::instance();

                try
                {
                    $info = ezcSystemInfo::getInstance();
                    $systemInfo = array(
                        'cpu_type' => $info->cpuType,
                        'cpu_speed' => $info->cpuSpeed,
                        'cpu_count' =>$info->cpuCount,
                        'memory_size' => $info->memorySize
                    );

                    $phpAcceleratorInfo = ( $info->phpAccelerator === null )
                        ? array()
                        : array(
                            'name' => $info->phpAccelerator->name,
                            'url' => $info->phpAccelerator->url,
                            'enabled' => $info->phpAccelerator->isEnabled,
                            'version_integer' => $info->phpAccelerator->versionInt,
                            'version_string' => $info->phpAccelerator->versionString
                        );
                }
                catch ( ezcSystemInfoReaderCantScanOSException $e )
                {
                    $systemInfo = array(
                        'cpu_type' => '',
                        'cpu_speed' => '',
                        'cpu_count' => '',
                        'memory_size' => ''
                    );
                    $phpAcceleratorInfo = array();
                }
                $tpl = eZTemplate::factory();

                $tpl->setVariable( 'ezpublish_version', "5.0 pre-alpha1 (Kilimanjaro)" );
                $tpl->setVariable( 'ezpublish_extensions', eZExtension::activeExtensions() );
                $tpl->setVariable( 'php_version', phpversion() );
                $tpl->setVariable( 'php_accelerator', $phpAcceleratorInfo );
                $tpl->setVariable( 'webserver_info', $webserverInfo );
                $tpl->setVariable( 'database_info', $db->databaseName() );
                $tpl->setVariable( 'database_charset', $db->charset() );
                $tpl->setVariable( 'database_object', $db );
                $tpl->setVariable( 'php_loaded_extensions', $loadedExtensions );
                $tpl->setVariable( 'autoload_functions', $splAutoloadFunctions );
                $tpl->setVariable( 'php_ini', $phpINI );

                return $tpl->fetch( "design:setup/info.tpl" );
            }
        );

        // Rendering with twig, embedding the result of a legacy template
        return $this->render(
            "eZDemoBundle::setupInfo.html.twig",
            array(
                "title" => "System information",
                "infoOutput" => $infoOutput,
            )
        );
    }
}
