<?php
// On Symfony container compilation*, reads parameters from env variables if defined and overrides the yml parameters.
// * For typical use cases like Docker, make sure to recompile Symfony container on run to refresh settings.

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

// Cache settings
// If CACHE_POOL env variable is set, check if there is a yml file that needs to be loaded for it
if (($pool = getenv('CACHE_POOL')) && file_exists(__DIR__ . "/../cache_pool/${pool}.yml")) {
    $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../cache_pool'));
    $loader->load("${pool}.yml");
}

if ($value = getenv('HTTPCACHE_DEFAULT_TTL')) {
    $container->setParameter('httpcache_default_ttl', $value);
}
