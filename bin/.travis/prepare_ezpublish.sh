#!/bin/sh

# Script to prepare eZPublish installation

# Clone legacy and set file permissions
echo "> Clone eZ Legacy"
git clone --depth 1 https://github.com/ezsystems/ezpublish-legacy.git ezpublish_legacy
cd ezpublish_legacy
echo "> Set legacy folders permissions"
sudo chown -R www-data:www-data extension/ var/ settings/ design/ autoload/
sudo chmod -R og+rwx extension/ var/ settings/ design/ autoload/
cd ..

# (composer) install dependencies
echo "> Install dependencies through composer"
composer install --dev --prefer-dist

# set folder permissions
echo "> Set folder permissions"
sudo chown -R www-data:www-data ezpublish/{cache,logs,config}
sudo chmod -R og+rwX ezpublish/{cache,logs,config}


# run other scripts
echo "> Run assetic dump"
php ezpublish/console --env=behat assetic:dump
