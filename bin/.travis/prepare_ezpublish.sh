#!/bin/sh

# Script to prepare eZPublish installation

echo "> Install dependencies through composer"
composer install --dev --prefer-dist

echo "> Set folder permissions"
sudo setfacl -R -m u:www-data:rwx -m u:www-data:rwx \
     ezpublish/{cache,logs,config,sessions} ezpublish_legacy/{design,extension,settings,var} web
sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx \
     ezpublish/{cache,logs,config,sessions} ezpublish_legacy/{design,extension,settings,var} web

echo "> Run assetic dump for behat env"
php ezpublish/console --env=behat assetic:dump
