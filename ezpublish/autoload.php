<?php
/**
 * File containing the autoload configuration.
 * It uses Composer autoloader and is greatly inspired by the Symfony standard distribution's.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var $loader ClassLoader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader( array( $loader, 'loadClass' ) );

return $loader;
