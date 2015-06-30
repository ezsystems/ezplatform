#!/bin/bash

# Script to prepare eZPublish installation

echo "> Setup github auth key to not reach api limit"
./bin/.travis/install_composer_github_key.sh

echo "> Set folder permissions"
sudo find {ezpublish/{cache,logs,config,sessions},web} -type d | sudo xargs chmod -R 777
sudo find {ezpublish/{cache,logs,config,sessions},web} -type f | sudo xargs chmod -R 666

echo "> Copy behat specific parameters.yml settings"
cp bin/.travis/parameters.yml ezpublish/config/

echo "> Install dependencies through composer"
composer install --no-progress --no-interaction

if [ "$INSTALL" = "demoContentNonUniqueDB" ] ; then
  echo "> Install ezplatform demo-content"
  php ezpublish/console ezplatform:install --env=behat --no-debug demo
else
  echo "> Install ezplatform clean"
  php ezpublish/console ezplatform:install --env=behat --no-debug clean
fi

echo "> Run assetic dump for behat env"
php ezpublish/console --env=behat --no-debug assetic:dump
