<?php
/**
 * File containing the EzPublishKernel class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

use Egulias\ListenersDebugCommandBundle\EguliasListenersDebugCommandBundle;
use eZ\Bundle\EzPublishCoreBundle\EzPublishCoreBundle;
use eZ\Bundle\EzPublishLegacyBundle\EzPublishLegacyBundle;
use eZ\Bundle\EzPublishRestBundle\EzPublishRestBundle;
use EzSystems\DemoBundle\EzSystemsDemoBundle;
use EzSystems\BehatBundle\EzSystemsEzPublishBehatBundle;
use eZ\Bundle\EzPublishCoreBundle\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\AsseticBundle\AsseticBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle;
use Sensio\Bundle\DistributionBundle\SensioDistributionBundle;
use Tedivm\StashBundle\TedivmStashBundle;

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
            new TedivmStashBundle(),
            new EzPublishCoreBundle(),
            new EzPublishLegacyBundle(),
            new EzSystemsDemoBundle(),
            new EzPublishRestBundle(),
        );

        switch ( $this->getEnvironment() )
        {
            case "test":
                $bundles[] = new EzSystemsEzPublishBehatBundle();
                // No break, test also needs dev bundles
            case "dev":
                $bundles[] = new WebProfilerBundle();
                $bundles[] = new SensioDistributionBundle();
                $bundles[] = new SensioGeneratorBundle();
                $bundles[] = new EguliasListenersDebugCommandBundle();
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
        $environment = $this->getEnvironment();
        $loader->load( __DIR__ . '/config/config_' . $environment . '.yml' );
        $configFile = __DIR__ . '/config/ezpublish_' . $environment . '.yml';

        if ( !is_file( $configFile ) )
        {
            $configFile = __DIR__ . '/config/ezpublish_setup.yml';
        }

        if ( !is_readable( $configFile ) )
        {
            throw new RuntimeException( "Configuration file '$configFile' is not readable." );
        }

        $loader->load( $configFile );
    }
}
