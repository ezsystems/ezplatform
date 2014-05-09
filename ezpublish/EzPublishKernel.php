<?php
/**
 * File containing the EzPublishKernel class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

use Egulias\ListenersDebugCommandBundle\EguliasListenersDebugCommandBundle;
use eZ\Bundle\EzPublishCoreBundle\EzPublishCoreBundle;
use eZ\Bundle\EzPublishDebugBundle\EzPublishDebugBundle;
use eZ\Bundle\EzPublishLegacyBundle\EzPublishLegacyBundle;
use eZ\Bundle\EzPublishRestBundle\EzPublishRestBundle;
use EzSystems\CommentsBundle\EzSystemsCommentsBundle;
use EzSystems\DemoBundle\EzSystemsDemoBundle;
use EzSystems\BehatBundle\EzSystemsBehatBundle;
use eZ\Bundle\EzPublishCoreBundle\Kernel;
use EzSystems\NgsymfonytoolsBundle\EzSystemsNgsymfonytoolsBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\AsseticBundle\AsseticBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle;
use Sensio\Bundle\DistributionBundle\SensioDistributionBundle;
use Tedivm\StashBundle\TedivmStashBundle;
use WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle;
use Nelmio\CorsBundle\NelmioCorsBundle;
use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;

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
            new SwiftmailerBundle(),
            new AsseticBundle(),
            new DoctrineBundle(),
            new TedivmStashBundle(),
            new HautelookTemplatedUriBundle(),
            new EzPublishCoreBundle(),
            new EzPublishLegacyBundle( $this ),
            new EzSystemsDemoBundle(),
            new EzPublishRestBundle(),
            new EzSystemsCommentsBundle(),
            new EzSystemsNgsymfonytoolsBundle(),
            new WhiteOctoberPagerfantaBundle(),
            new NelmioCorsBundle(),
        );

        switch ( $this->getEnvironment() )
        {
            case "test":
            case "behat":
                $bundles[] = new EzSystemsBehatBundle();
                // No break, test also needs dev bundles
            case "dev":
                $bundles[] = new EzPublishDebugBundle();
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
