<?php
/**
 * File containing the EzPublishKernel class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

use eZ\Bundle\EzPublishCoreBundle\EzPublishCoreBundle;
use eZ\Bundle\EzPublishLegacyBundle\EzPublishLegacyBundle;
use Symfony\Component\HttpKernel\Kernel;
use EzSystems\DemoBundle\EzSystemsDemoBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\AsseticBundle\AsseticBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;

class EzPublishKernel extends Kernel
{
    /**
     * Constructor.
     *
     * @param string $environment The environment
     * @param bool $debug Whether to enable debugging or not
     */
    public function __construct( $environment, $debug )
    {
        parent::__construct( $environment, $debug );
        $this->loadClassCache();
    }

    /**
     * Returns an array of bundles to registers.
     *
     * @return array An array of bundle instances.
     *
     * @api
     */
    public function registerBundles()
    {
        $bundles = array(
            new FrameworkBundle(),
            new TwigBundle(),
            new AsseticBundle(),
            new EzPublishCoreBundle(),
            new EzPublishLegacyBundle(),
            new EzSystemsDemoBundle(),
        );

        if ( $this->getEnvironment() === 'dev' )
        {
            $bundles[] = new WebProfilerBundle();
        }

        return $bundles;
    }

    /**
     * Loads the container configuration
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     *
     * @api
     */
    public function registerContainerConfiguration( LoaderInterface $loader )
    {
        $loader->load( __DIR__ . '/config/config_' . $this->getEnvironment() . '.yml' );
    }
}
