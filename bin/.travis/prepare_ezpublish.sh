#!/bin/bash

# Script to prepare eZPublish installation

echo "> prefer ip4 to avoid packagist.org composer issues"
sudo sh -c "echo 'precedence ::ffff:0:0/96 100' >> /etc/gai.conf"

echo "> Setup github auth key to not reach api limit"
cp bin/.travis/composer-auth.json ~/.composer/auth.json

echo "> Set folder permissions"
chmod -R a+rwX app/cache app/logs web

echo "> Copy behat specific parameters.yml settings"
cp bin/.travis/parameters.yml app/config/

# Switch to another Symfony version if asked for (with composer update to not use composer.lock if present)
if [ "$SYMFONY_VERSION" != "" ] ; then
    echo "> Install dependencies through Composer (with custom Symfony version: ${SYMFONY_VERSION})"
    composer require --no-update symfony/symfony="${SYMFONY_VERSION}"
    composer update --no-progress --no-interaction --prefer-dist
else
    echo "> Install dependencies through Composer"
    composer install --no-progress --no-interaction --prefer-dist
fi

echo "> Run assetic dump for behat env"
php app/console --env=behat --no-debug assetic:dump

echo "> Installing ezplatform clean"
php app/console --env=behat ezplatform:install clean
