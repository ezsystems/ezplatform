#!/bin/bash

# Script to prepare eZPublish installation

echo "> prefer ip4 to avoid packagist.org composer issues"
sudo sh -c "echo 'precedence ::ffff:0:0/96 100' >> /etc/gai.conf"

echo "> Setup github auth key to not reach api limit"
./bin/.travis/install_composer_github_key.sh

echo "> Set folder permissions"
sudo find {app/{cache,logs},web} -type d | sudo xargs chmod -R 777
sudo find {app/{cache,logs},web} -type f | sudo xargs chmod -R 666

echo "> Copy behat specific parameters.yml settings"
cp bin/.travis/parameters.yml app/config/

# Switch to another Symfony version if asked for
if [ "$SYMFONY_VERSION" != "" ] ; then composer require --no-update symfony/symfony="$SYMFONY_VERSION" ; fi;

echo "> Install dependencies through composer"
composer install --no-progress --no-interaction

echo "> Run assetic dump for behat env"
php app/console --env=behat --no-debug assetic:dump

echo "> Installing ezplatform clean"
php app/console --env=behat ezplatform:install clean

echo "> Warm up cache, using curl to make sure everything is warmed up, incl class, http & spi cache"
curl -sSLI "http://localhost"
