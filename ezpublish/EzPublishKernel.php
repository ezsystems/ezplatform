<?php
/**
 * File containing the EzPublishKernel class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

use eZ\Bundle\EzPublishCoreBundle\EzPublishCoreBundle;
use Egulias\ListenersDebugCommandBundle\EguliasListenersDebugCommandBundle;
use eZ\Bundle\EzPublishLegacyBundle\EzPublishLegacyBundle;
use eZ\Bundle\EzPublishRestBundle\EzPublishRestBundle;
use EzSystems\DemoBundle\EzSystemsDemoBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\AsseticBundle\AsseticBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle;
use Tedivm\StashBundle\TedivmStashBundle;
use Sensio\Bundle\DistributionBundle\SensioDistributionBundle;

class EzPublishKernel extends Kernel
{
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
            new MonologBundle(),
            new AsseticBundle(),
            new SensioGeneratorBundle(),
            new TedivmStashBundle(),
            new EzPublishCoreBundle(),
            new EzPublishLegacyBundle(),
            new EzSystemsDemoBundle(),
            new EzPublishRestBundle(),
            new SensioDistributionBundle(),
        );

        if ( $this->getEnvironment() === 'dev' )
        {
            $bundles[] = new WebProfilerBundle();
            $bundles[] = new EguliasListenersDebugCommandBundle();
            $bundles[] = new SensioDistributionBundle();
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
        catch ( \InvalidArgumentException $e )
        {
            $loader->load( __DIR__ . '/config/ezpublish_setup.yml' );
        }
    }
}
