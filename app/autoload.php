<?php
/**
 * File containing the autoload configuration.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

require_once __DIR__ . '/../vendor/symfony/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
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
    require_once __DIR__ . '/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

    $loader->registerPrefixFallbacks(array(__DIR__ . '/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs'));
}

// "Project" bundles goes in src/ directory
$loader->registerNamespaceFallbacks(
    array(
         __DIR__ . '/../src',
    )
);
$loader->register();
