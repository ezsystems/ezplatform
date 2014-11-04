#!/bin/bash

# Script to prepare eZPublish installation

echo "> Setup github auth key to not reach api limit"
./bin/.travis/install_composer_github_key.sh

echo "> Install dependencies through composer"
composer install --dev --prefer-dist

echo "> Set folder permissions"
sudo find {ezpublish/{cache,logs,config,sessions},ezpublish_legacy/{design,extension,settings,var},web} -type d | sudo xargs chmod -R 777
sudo find {ezpublish/{cache,logs,config,sessions},ezpublish_legacy/{design,extension,settings,var},web} -type f | sudo xargs chmod -R 666

echo "> Run assetic dump for behat env"
php ezpublish/console --env=behat assetic:dump

echo "> Clear and warm up caches for behat env"
php ezpublish/console cache:clear --env=behat --no-debug
