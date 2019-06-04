#!/bin/bash
# Install REST package to get its dev dependencies and use them to run tests

cd ./vendor/ezsystems/ezplatform-rest
composer install
php ./vendor/bin/phpunit -c phpunit-integration-rest.xml
