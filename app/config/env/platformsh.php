<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

$relationships = getenv('PLATFORM_RELATIONSHIPS');
if (!$relationships) {
    return;
}

$relationships = json_decode(base64_decode($relationships), true);

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
    $container->setParameter('database_path', '');
}

if (!empty($relationships['redis'][0])) {
    // Configure redis cache pool to use for the install.
    $container->setParameter('cache_pool', 'singleredis');
    $container->setParameter('cache_host', $relationships['redis'][0]['host']);
    $container->setParameter('cache_redis_port', $relationships['redis'][0]['port']);

    $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../cache_pool'));
    $loader->load('singleredis.yml');
}

# Store session into /tmp.
ini_set('session.save_path', '/tmp/sessions');
