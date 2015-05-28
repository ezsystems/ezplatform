<?php
/**
 * File containing the EzPublishKernel class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

use eZ\Bundle\EzPublishCoreBundle\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

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
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Tedivm\StashBundle\TedivmStashBundle(),
            new Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new FOS\HttpCacheBundle\FOSHttpCacheBundle(),
            new eZ\Bundle\EzPublishCoreBundle\EzPublishCoreBundle(),
            new eZ\Bundle\EzPublishLegacySearchEngineBundle\EzPublishLegacySearchEngineBundle(),
            new eZ\Bundle\EzPublishIOBundle\EzPublishIOBundle(),
            new EzSystems\DemoBundle\EzSystemsDemoBundle(),
            new eZ\Bundle\EzPublishRestBundle\EzPublishRestBundle(),
            new EzSystems\CommentsBundle\EzSystemsCommentsBundle(),
            new EzSystems\PlatformUIAssetsBundle\EzSystemsPlatformUIAssetsBundle(),
            new EzSystems\PlatformUIBundle\EzSystemsPlatformUIBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new WhiteOctober\BreadcrumbsBundle\WhiteOctoberBreadcrumbsBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Oneup\FlysystemBundle\OneupFlysystemBundle(),
            new EzSystems\PlatformInstallerBundle\EzSystemsPlatformInstallerBundle(),
            new EzSystems\PrivacyCookieBundle\EzSystemsPrivacyCookieBundle()
        );

        switch ( $this->getEnvironment() )
        {
            case "test":
            case "behat":
                $bundles[] = new EzSystems\BehatBundle\EzSystemsBehatBundle();
                // No break, test also needs dev bundles
            case "dev":
                $bundles[] = new eZ\Bundle\EzPublishDebugBundle\EzPublishDebugBundle();
                $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
                $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
                $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
                $bundles[] = new Egulias\ListenersDebugCommandBundle\EguliasListenersDebugCommandBundle();
        }

        return $bundles;
    }

    /**
     * Loads the container configuration
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     * @throws \RuntimeException when config file is not readable
     *
     * @api
     */
    public function registerContainerConfiguration( LoaderInterface $loader )
    {
        $environment = $this->getEnvironment();
        $loader->load( __DIR__ . '/config/config_' . $environment . '.yml' );
        $configFile = __DIR__ . '/config/ezpublish_' . $environment . '.yml';

        // if config file or base config file is missing, then install is not done yet
        if ( !is_file( $configFile ) || !is_file( __DIR__ . '/config/ezpublish.yml' ) )
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
