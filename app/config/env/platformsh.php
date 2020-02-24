<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

// Run for all hooks, incl build step
if (getenv('PLATFORM_PROJECT_ENTROPY')) {
    // Disable PHPStormPass as we don't have write access & it's not localhost
    $container->setParameter('ezdesign.phpstorm.enabled', false);
}

// Will not be executed on build step
$relationships = getenv('PLATFORM_RELATIONSHIPS');
if (!$relationships) {
    return;
}
$routes = getenv('PLATFORM_ROUTES');

$relationships = json_decode(base64_decode($relationships), true);
$routes = json_decode(base64_decode($routes), true);

foreach ($relationships['database'] as $endpoint) {
    if (empty($endpoint['query']['is_master'])) {
        continue;
    }

    $container->setParameter('database_driver', 'pdo_' . $endpoint['scheme']);
    $container->setParameter('database_host', $endpoint['host']);
    $container->setParameter('database_port', $endpoint['port']);
    $container->setParameter('database_name', $endpoint['path']);
    $container->setParameter('database_user', $endpoint['username']);
    $container->setParameter('database_password', $endpoint['password']);

    // 'cluster_database_name' is deprecated in eZ Platform 1.13.1/2.1
    // Cluster DB name is hardcoded. It will have no any effect if cluster is disabled
    $container->setParameter('cluster_database_name', 'cluster');
}

// If mailer_host has default value, then set it to platform.sh' default instead
if ($container->getParameter('mailer_host') === '127.0.0.1') {
    $container->setParameter('mailer_host', getenv('PLATFORM_SMTP_HOST'));
}

// PLATFORMSH_DFS_NFS_PATH is different compared to DFS_NFS_PATH in the sense that it is relative to ezplatform dir
// DFS_NFS_PATH is an absolute path
if ($dfsNfsPath = getenv('PLATFORMSH_DFS_NFS_PATH')) {
    $container->setParameter('dfs_nfs_path', sprintf('%s/%s', dirname($container->getParameter('kernel.root_dir')), $dfsNfsPath));

    if (array_key_exists('dfs_database', $relationships)) {
        foreach ($relationships['dfs_database'] as $endpoint) {
            if (empty($endpoint['query']['is_master'])) {
                continue;
            }

            $container->setParameter('dfs_database_driver', 'pdo_' . $endpoint['scheme']);
            $container->setParameter('dfs_database_host', $endpoint['host']);
            $container->setParameter('dfs_database_port', $endpoint['port']);
            $container->setParameter('dfs_database_name', $endpoint['path']);
            $container->setParameter('dfs_database_user', $endpoint['username']);
            $container->setParameter('dfs_database_password', $endpoint['password']);
        }
    } else {
        // If dfs_database endpoint is not defined, we'll use the default database for DFS too
        $container->setParameter('dfs_database_driver', $container->getParameter('database_driver'));
        $container->setParameter('dfs_database_host', $container->getParameter('database_host'));
        $container->setParameter('dfs_database_port', $container->getParameter('database_port'));
        $container->setParameter('dfs_database_name', $container->getParameter('database_name'));
        $container->setParameter('dfs_database_user', $container->getParameter('database_user'));
        $container->setParameter('dfs_database_password', $container->getParameter('database_password'));
    }

    $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../dfs'));
    $loader->load('dfs.yml');
}
// Use Redis-based caching if possible.
if (isset($relationships['rediscache'])) {
    foreach ($relationships['rediscache'] as $endpoint) {
        if ($endpoint['scheme'] !== 'redis') {
            continue;
        }

        $container->setParameter('cache_pool', 'cache.redis');
        $container->setParameter('cache_dsn', sprintf('%s:%d', $endpoint['host'], $endpoint['port']) . '?retry_interval=3');

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../cache_pool'));
        $loader->load('cache.redis.yml');
    }
} elseif (isset($relationships['cache'])) {
    // Fallback to memcached if here (deprecated, we will only handle redis here in the future)
    foreach ($relationships['cache'] as $endpoint) {
        if ($endpoint['scheme'] !== 'memcached') {
            continue;
        }

        @trigger_error('Usage of Memcached is deprecated, redis is recommended', E_USER_DEPRECATED);

        $container->setParameter('cache_pool', 'cache.memcached');
        $container->setParameter('cache_dsn', sprintf('%s:%d', $endpoint['host'], $endpoint['port']));

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../cache_pool'));
        $loader->load('cache.memcached.yml');
    }
}

// Use Redis-based sessions if possible. If a separate Redis instance
// is available, use that.  If not, share a Redis instance with the
// Cache.  (That should be safe to do except on especially high-traffic sites.)
if (isset($relationships['redissession'])) {
    foreach ($relationships['redissession'] as $endpoint) {
        if ($endpoint['scheme'] !== 'redis') {
            continue;
        }

        $container->setParameter('ezplatform.session.handler_id', 'ezplatform.core.session.handler.native_redis');
        $container->setParameter('ezplatform.session.save_path', sprintf('%s:%d', $endpoint['host'], $endpoint['port']));
    }
} elseif (isset($relationships['rediscache'])) {
    foreach ($relationships['rediscache'] as $endpoint) {
        if ($endpoint['scheme'] !== 'redis') {
            continue;
        }

        $container->setParameter('ezplatform.session.handler_id', 'ezplatform.core.session.handler.native_redis');
        $container->setParameter('ezplatform.session.save_path', sprintf('%s:%d', $endpoint['host'], $endpoint['port']));
    }
}

if (isset($relationships['solr'])) {
    foreach ($relationships['solr'] as $endpoint) {
        if ($endpoint['scheme'] !== 'solr') {
            continue;
        }

        $container->setParameter('search_engine', 'solr');
        $container->setParameter('solr_dsn', sprintf('http://%s:%d/%s', $endpoint['host'], $endpoint['port'], 'solr'));
        // To set solr_core parameter we assume path is in form like: "solr/collection1"
        $container->setParameter('solr_core', substr($endpoint['path'], 5));
    }
}

// We will pick a varnish route by the following prioritization:
// - The first route found that has upstream: varnish
// - if primary route has upstream: varnish, that route will be prioritised
// If no route is found with upstream: varnish, then purge_server will not be set
$route = null;
foreach ($routes as $host => $info) {
    if ($route === null && $info['type'] === 'upstream' && $info['upstream'] === 'varnish') {
        $route = $host;
    }
    if ($info['type'] === 'upstream' && $info['upstream'] === 'varnish' && $info['primary'] === true) {
        $route = $host;
        break;
    }
}

if ($route !== null && !getenv('HTTPCACHE_PURGE_TYPE')) {
    $container->setParameter('purge_type', 'varnish');
    $container->setParameter('purge_server', rtrim($route, '/'));
}

// Setting default value for HTTPCACHE_VARNISH_INVALIDATE_TOKEN if it is not explicitly set
if (!getenv('HTTPCACHE_VARNISH_INVALIDATE_TOKEN')) {
    $container->setParameter('varnish_invalidate_token', getenv('PLATFORM_PROJECT_ENTROPY'));
}

// Adapt config based on enabled PHP extensions
// Get imagine to use imagick if enabled, to avoid using php memory for image convertions
if (extension_loaded('imagick')) {
    $container->setParameter('liip_imagine_driver', 'imagick');
}
