<?php
// On Symfony container compilation*, reads parameters from env variables if defined and overrides the yml parameters.
// * For typical use cases like Docker, make sure to recompile Symfony container on run to refresh settings.

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

if ($value = getenv('SYMFONY_SECRET')) {
    $container->setParameter('secret', $value);
}

// Mailer settings
if ($value = getenv('MAILER_TRANSPORT')) {
    $container->setParameter('mailer_transport', $value);
}

if ($value = getenv('MAILER_HOST')) {
    $container->setParameter('mailer_host', $value);
}

if ($value = getenv('MAILER_USER')) {
    $container->setParameter('mailer_user', $value);
}

if ($value = getenv('MAILER_PASSWORD')) {
    $container->setParameter('mailer_password', $value);
}

// Database settings
if ($value = getenv('DATABASE_DRIVER')) {
    $container->setParameter('database_driver', $value);
}

if ($value = getenv('DATABASE_HOST')) {
    $container->setParameter('database_host', $value);
}

if ($value = getenv('DATABASE_PORT')) {
    $container->setParameter('database_port', $value);
}

if ($value = getenv('DATABASE_NAME')) {
    $container->setParameter('database_name', $value);
}

if ($value = getenv('DATABASE_USER')) {
    $container->setParameter('database_user', $value);
}

if ($value = getenv('DATABASE_PASSWORD')) {
    $container->setParameter('database_password', $value);
}

// Search Engine settings
if ($value = getenv('SEARCH_ENGINE')) {
    $container->setParameter('search_engine', $value);
}

if ($value = getenv('SOLR_DSN')) {
    $container->setParameter('solr_dsn', $value);
}

// Logging settings
if ($value = getenv('LOG_TYPE')) {
    $container->setParameter('log_type', $value);
}

if ($value = getenv('LOG_PATH')) {
    $container->setParameter('log_path', $value);
}

// Cache settings
// Config validation by Stash prohbitis us from pre defining pools using drivers not supported by all systems
// So we expose a env variable to load and use other pools when needed, additional pools can be added in cache_pool/ folder.
if ($pool = getenv('CUSTOM_CACHE_POOL')) {
    $container->setParameter('cache_pool', $pool);

    if ($host = getenv('CACHE_HOST')) {
        $container->setParameter('cache_host', $host);
    }

    // Optional port settings in case not default
    if ($host = getenv('CACHE_MEMCACHED_PORT')) {
        $container->setParameter('cache_memcached_port', $host);
    } elseif ($host = getenv('CACHE_REDIS_PORT')) {
        $container->setParameter('cache_redis_port', $host);
    }

    $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../cache_pool'));
    $loader->load($pool . '.yml');
}

// HttpCache setting (for configuring http cache purging)
if ($purgeType = getenv('HTTPCACHE_PURGE_TYPE')) {
    $container->setParameter('purge_type', $purgeType);
}

if ($purgeServer = getenv('HTTPCACHE_PURGE_SERVER')) {
    // BC : In earlier versions, purge_type was set automatically if purge_server was set
    if ($purgeType === false) {
        $container->setParameter('purge_type', 'http');
    }
    $container->setParameter('purge_server', $purgeServer);
}

if ($value = getenv('HTTPCACHE_DEFAULT_TTL')) {
    $container->setParameter('httpcache_default_ttl', $value);
}

// EzSystemsRecommendationsBundle settings
if ($value = getenv('RECOMMENDATIONS_CUSTOMER_ID')) {
    $container->setParameter('ez_recommendation.default.yoochoose.customer_id', $value);
}

if ($value = getenv('RECOMMENDATIONS_LICENSE_KEY')) {
    $container->setParameter('ez_recommendation.default.yoochoose.license_key', $value);
}

if ($value = getenv('PUBLIC_SERVER_URI')) {
    $container->setParameter('ez_recommendation.default.server_uri', $value);
}

// EzSystemsPlatformFastlyCacheBundle settings
if ($value = getenv('FASTLY_SERVICE_ID')) {
    $container->setParameter('fastly_service_id', $value);
}

if ($value = getenv('FASTLY_KEY')) {
    $container->setParameter('fastly_key', $value);
}
