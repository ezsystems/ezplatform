#!/bin/bash

# Script to prepare eZPublish installation

echo "> Setup github auth key to not reach api limit"
./bin/.travis/install_composer_github_key.sh

echo "> Add legacy-bridge to requirements"
composer require --no-update "ezsystems/legacy-bridge:dev-master"

echo "> Configuring legacy"
php ./bin/.travis/enablelegacybundle.php

## add legacy routes
cat << EOF >> ezpublish/config/routing.yml
_ezpublishLegacyRoutes:
    resource: @EzPublishLegacyBundle/Resources/config/routing.yml
EOF

## add setup wizard security rule
cat << EOF >> ezpublish/config/security.yml
        ezpublish_setup:
            pattern: ^/ezsetup
            security: false

EOF

## enable legacy mode on admin siteaccess
cat << EOF >> ezpublish/config/ezpublish_behat.yml
ez_publish_legacy:
    system:
        behat_site:
            legacy_mode: false
        behat_site_admin:
            legacy_mode: true

EOF

## add legacy post-*-cmd scripts
php ./bin/.travis/add_legacy_composer_scripts.php

## enable legacy template engine
sed -i "s/engines: \['twig/engines: ['eztpl', 'twig/" ezpublish/config/config.yml

echo "> Install dependencies through composer"
composer install --dev --prefer-dist

echo "> Set folder permissions"
sudo find {ezpublish/{cache,logs,config,sessions},web} -type d | sudo xargs chmod -R 777
sudo find {ezpublish/{cache,logs,config,sessions},web} -type f | sudo xargs chmod -R 666

echo "> Run assetic dump for behat env"
php ezpublish/console --env=behat assetic:dump

echo "> Clear and warm up caches for behat env"
php ezpublish/console cache:clear --env=behat --no-debug
