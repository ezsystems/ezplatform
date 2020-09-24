<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

require_once dirname(__DIR__, 2).'/bootstrap.php';

// Run for all hooks, incl build step
if ($_SERVER['PLATFORM_PROJECT_ENTROPY'] ?? false) {
    // Disable PHPStormPass as we don't have write access & it's not localhost
    $container->setParameter('ezdesign.phpstorm.enabled', false);
}

// Will not be executed on build step
$relationships = $_SERVER['PLATFORM_RELATIONSHIPS'] ?? false;
if (!$relationships) {
    return;
}
$routes = $_SERVER['PLATFORM_ROUTES'];

$relationships = json_decode(base64_decode($relationships), true);
$routes = json_decode(base64_decode($routes), true);

// PLATFORMSH_DFS_NFS_PATH is different compared to DFS_NFS_PATH in the sense that it is relative to ezplatform dir
// DFS_NFS_PATH is an absolute path
if ($dfsNfsPath = $_SERVER['PLATFORMSH_DFS_NFS_PATH'] ?? false) {
    $container->setParameter('dfs_nfs_path', sprintf('%s/%s', $container->getParameter('kernel.project_dir'), $dfsNfsPath));

    // Map common parameters
    $container->setParameter('dfs_database_charset', $container->getParameter('database_charset'));
    $container->setParameter(
        'dfs_database_collation',
        $container->getParameter('database_collation')
    );
    if (array_key_exists('dfs_database', $relationships)) {
        // process dedicated P.sh dedicated config
        foreach ($relationships['dfs_database'] as $endpoint) {
            if (empty($endpoint['query']['is_master'])) {
                continue;
            }
            $container->setParameter('dfs_database_driver', 'pdo_' . $endpoint['scheme']);
            $container->setParameter(
                'dfs_database_url',
                sprintf(
                    '%s://%s:%s:%d@%s/%s',
                    $endpoint['scheme'],
                    $endpoint['username'],
                    $endpoint['password'],
                    $endpoint['port'],
                    $endpoint['host'],
                    $endpoint['path']
                )
            );
        }
    } else {
        // or set fallback from the Repository database, if not configured
        $container->setParameter('dfs_database_driver', $container->getParameter('database_driver'));
    }

    $loader = new Loader\YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/dfs'));
    $loader->load('dfs.yaml');
}
// Use Redis-based caching if possible.
if (isset($relationships['rediscache'])) {
    foreach ($relationships['rediscache'] as $endpoint) {
        if ($endpoint['scheme'] !== 'redis') {
            continue;
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/cache_pool'));
        $loader->load('cache.redis.yaml');

        $container->setParameter('cache_pool', 'cache.redis');
        $container->setParameter('cache_dsn', sprintf('%s:%d', $endpoint['host'], $endpoint['port']) . '?retry_interval=3');
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

        $loader = new Loader\YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/cache_pool'));
        $loader->load('cache.memcached.yaml');
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

if ($route !== null && !($_SERVER['HTTPCACHE_PURGE_TYPE'] ?? false)) {
    $purgeServer = rtrim($route, '/');
    if (($_SERVER['HTTPCACHE_USERNAME'] ?? false) && ($_SERVER['HTTPCACHE_PASSWORD'] ?? false)) {
        $domain = parse_url($purgeServer, \PHP_URL_HOST);
        $credentials = urlencode($_SERVER['HTTPCACHE_USERNAME']) . ':' . urlencode($_SERVER['HTTPCACHE_PASSWORD']);
        $purgeServer = str_replace($domain, $credentials . '@' . $domain, $purgeServer);
    }

    $container->setParameter('purge_type', 'varnish');
    $container->setParameter('purge_server', $purgeServer);
}
// Setting default value for HTTPCACHE_VARNISH_INVALIDATE_TOKEN if it is not explicitly set
if (!($_SERVER['HTTPCACHE_VARNISH_INVALIDATE_TOKEN'] ?? false)) {
    $container->setParameter('varnish_invalidate_token', $_SERVER['PLATFORM_PROJECT_ENTROPY']);
}

// Adapt config based on enabled PHP extensions
// Get imagine to use imagick if enabled, to avoid using php memory for image conversions
if (extension_loaded('imagick')) {
    $container->setParameter('liip_imagine_driver', 'imagick');
}
