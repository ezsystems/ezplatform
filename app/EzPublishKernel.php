<?php
/**
 * File containing the EzPublishKernel class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

use eZ\Bundle\EzPublishCoreBundle\EzPublishCoreBundle,
    eZ\Bundle\EzPublishLegacyBundle\EzPublishLegacyBundle,
    eZ\Bundle\EzPublishRestBundle\EzPublishRestBundle,
    EzSystems\DemoBundle\EzSystemsDemoBundle,
    Symfony\Component\HttpKernel\Kernel,
    Symfony\Bundle\FrameworkBundle\FrameworkBundle,
    Symfony\Bundle\SecurityBundle\SecurityBundle,
    Symfony\Bundle\TwigBundle\TwigBundle,
    Symfony\Bundle\AsseticBundle\AsseticBundle,
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle,
    Symfony\Component\Config\Loader\LoaderInterface,
    Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle;

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
            new SecurityBundle(),
            new TwigBundle(),
            new AsseticBundle(),
            new SensioGeneratorBundle(),
            new EzPublishCoreBundle(),
            new EzPublishLegacyBundle(),
            new EzSystemsDemoBundle(),
            new EzPublishRestBundle(),
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
        try
        {
            $loader->load( __DIR__ . '/config/ezpublish_' . $this->getEnvironment() . '.yml' );
        }
        catch( \InvalidArgumentException $e )
        {
            $loader->load( __DIR__ . '/config/ezpublish_setup.yml' );
        }
    }
}
