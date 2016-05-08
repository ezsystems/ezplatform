#!/usr/bin/env ash

if [ ! -d vendor ]; then
    curl -sS https://getcomposer.org/installer | php
    php -d memory_limit=-1 composer.phar install --no-progress --no-interaction --prefer-dist
    php -d memory_limit=-1 app/console ezplatform:install --env prod clean
    # Configure Memcache (@todo should be done somewhere else)
echo "
stash:
    tracking: false
    caches:
        default:
            drivers: [ Memcache ]
            inMemory: true
            registerDoctrineAdapter: false
            Memcache:
                prefix_key: 'docker'
                retry_timeout: 1
                servers:
                    -
                        server: memcache
                        port: 11211

" >> ${CONTAINER_PROJECT_DIR}/app/config/ezplatform.yml
fi

echo "ez" > ${CONTAINER_PROJECT_DIR}/.installok
exec "$@"
