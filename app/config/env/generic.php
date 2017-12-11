<?php
// On Symfony container compilation*, reads parameters from env variables if defined and overrides the yml parameters.
// * For typical use cases like Docker, make sure to recompile Symfony container on run to refresh settings.

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

// Cache settings
// If CACHE_POOL env variable is set, check if there is a yml file that needs to be loaded for it
if (($pool = getenv('CACHE_POOL')) && file_exists(__DIR__ . "/../cache_pool/${pool}.yml")) {
    $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../cache_pool'));
    $loader->load($pool . '.yml');
}

// Params that needs to be set at compile time and thus can't use Symfony's env()
if ($purgeType = getenv('HTTPCACHE_PURGE_TYPE')) {
    $container->setParameter('purge_type', $purgeType);
}

if ($value = getenv('MAILER_TRANSPORT')) {
    $container->setParameter('mailer_transport', $value);
}

if ($value = getenv('LOG_TYPE')) {
    $container->setParameter('log_type', $value);
}

// EzSystemsRecommendationsBundle settings
// @todo Move to use env() and params
if ($value = getenv('RECOMMENDATIONS_CUSTOMER_ID')) {
    $container->setParameter('ez_recommendation.default.yoochoose.customer_id', $value);
}

if ($value = getenv('RECOMMENDATIONS_LICENSE_KEY')) {
    $container->setParameter('ez_recommendation.default.yoochoose.license_key', $value);
}

if ($value = getenv('PUBLIC_SERVER_URI')) {
    $container->setParameter('ez_recommendation.default.server_uri', $value);
}
