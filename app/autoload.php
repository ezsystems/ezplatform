<?php
/**
 * File containing the autoload configuration.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

require_once __DIR__ . '/../vendor/symfony/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

// SessionHandlerInterface is native as of PHP 5.4, but we need forward compatibility
if ( version_compare( PHP_VERSION, '5.4', '<' ) )
    require_once __DIR__ . '/../vendor/symfony/symfony/src/Symfony/Component/HttpFoundation/Resources/stubs/SessionHandlerInterface.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;
use Symfony\Component\ClassLoader\ApcUniversalClassLoader;
use Symfony\Component\ClassLoader\MapClassLoader;

if ( extension_loaded( "APC" ) )
{
    require_once __DIR__ . '/../vendor/symfony/symfony/src/Symfony/Component/ClassLoader/ApcUniversalClassLoader.php';
    $loader = new ApcUniversalClassLoader( "eZPublish" );
}
else
{
    $loader = new UniversalClassLoader( "prefix" );
}
$loader->registerNamespaces(
    array(
         'Symfony'          => __DIR__ . '/../vendor/symfony/symfony/src',
         'eZ'      => array(
             __DIR__ . '/../vendor/ezsystems/ezp-mvc',
             __DIR__ . '/../vendor/ezsystems/api'
         ),
    )
);
$loader->registerPrefixes(
    array(
         'Twig_Extensions_' => __DIR__ . '/../vendor/twig/twig-extensions/lib',
         'Twig_'            => __DIR__ . '/../vendor/twig/twig/lib',
    )
);

// intl
if (!function_exists('intl_get_error_code'))
{
    require_once __DIR__ . '/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

    $loader->registerPrefixFallbacks(array(__DIR__ . '/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs'));
}

// "Project" bundles goes in src/ directory
$loader->registerNamespaceFallbacks(
    array(
         __DIR__ . '/../src',
    )
);
$loader->register();

// Classmap based autoloading
$classMap = include __DIR__ . '/../vendor/composer/autoload_classmap.php';
if ( !empty( $classMap ) )
{
    require_once __DIR__ . '/../vendor/symfony/symfony/src/Symfony/Component/ClassLoader/MapClassLoader.php';
    $classMapLoader = new MapClassLoader( $classMap );
    $classMapLoader->register();
}
