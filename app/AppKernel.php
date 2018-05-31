<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    /**
     * Returns an array of bundles to registers.
     *
     * @return array an array of bundle instances
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
            new EzSystems\PlatformHttpCacheBundle\EzSystemsPlatformHttpCacheBundle(),
            new eZ\Bundle\EzPublishCoreBundle\EzPublishCoreBundle(),
            new eZ\Bundle\EzPublishLegacySearchEngineBundle\EzPublishLegacySearchEngineBundle(),
            new eZ\Bundle\EzPublishIOBundle\EzPublishIOBundle(),
            new EzSystems\MultiFileUploadBundle\EzSystemsMultiFileUploadBundle(),
            new eZ\Bundle\EzPublishRestBundle\EzPublishRestBundle(),
            new EzSystems\PlatformUIAssetsBundle\EzSystemsPlatformUIAssetsBundle(),
            new EzSystems\PlatformUIBundle\EzSystemsPlatformUIBundle(),
            new EzSystems\EzSupportToolsBundle\EzSystemsEzSupportToolsBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new Oneup\FlysystemBundle\OneupFlysystemBundle(),
            new EzSystems\PlatformInstallerBundle\EzSystemsPlatformInstallerBundle(),
            new EzSystems\RepositoryFormsBundle\EzSystemsRepositoryFormsBundle(),
            new EzSystems\EzPlatformSolrSearchEngineBundle\EzSystemsEzPlatformSolrSearchEngineBundle(),
            new EzSystems\EzContentOnTheFlyBundle\EzSystemsEzContentOnTheFlyBundle(),
            new EzSystems\EzPlatformDesignEngineBundle\EzPlatformDesignEngineBundle(),
            new Bazinga\Bundle\JsTranslationBundle\BazingaJsTranslationBundle(),
            new JMS\TranslationBundle\JMSTranslationBundle(),
            new AppBundle\AppBundle(),
        );

        switch ($this->getEnvironment()) {
            case 'test':
            case 'behat':
                $bundles[] = new EzSystems\BehatBundle\EzSystemsBehatBundle();
                $bundles[] = new EzSystems\PlatformBehatBundle\EzPlatformBehatBundle();
                // No break, test also needs dev bundles
            case 'dev':
                $bundles[] = new eZ\Bundle\EzPublishDebugBundle\EzPublishDebugBundle();
                $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
                $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
                $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
                $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
                $bundles[] = new Egulias\ListenersDebugCommandBundle\EguliasListenersDebugCommandBundle();
        }

        return $bundles;
    }

    public function getCacheDir()
    {
        if (!empty($_SERVER['SYMFONY_CACHE_DIR'])) {
            return rtrim($_SERVER['SYMFONY_CACHE_DIR'], '/') . '/app/cache/' . $this->getEnvironment();
        }

        return parent::getCacheDir();
    }

    /**
     * Loads the container configuration.
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     * @throws \RuntimeException when config file is not readable
     *
     * @api
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.yml');
    }
}
