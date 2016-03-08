#! /bin/bash

if [[ "$SYMFONY_DEBUG" == "" && "$TRAVIS_PHP_VERSION" != "" && "$TRAVIS_PHP_VERSION" != "hhvm" ]]; then
    echo "> Disable xdebug";
    phpenv config-rm xdebug.ini ;
fi
