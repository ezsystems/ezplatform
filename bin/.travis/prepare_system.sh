#!/bin/bash

# Script to do tasks before install, can install system packages / software
## See http://about.travis-ci.org/docs/user/build-configuration/
##
## @todo Initial setup (before_install+before_scripts) currently takes about 3 minutes,
##       can be reduced if needed by using parallel download techniques as found in:
##       https://github.com/facebook/hiphop-php/commit/4add8586c5d9e4eee20fe15ccd78db9e9c6b56aa
##       https://github.com/facebook/hiphop-php/commit/0b2dfdf4492eb06a125b068e939d092ec0588e5c

# Disable xdebug to speed things up
if [[ "$USE_DEBUGGING" == "" && "$TRAVIS_PHP_VERSION" != "" && "$TRAVIS_PHP_VERSION" != "hhvm" ]]; then
    echo "> Disable xdebug";
    phpenv config-rm xdebug.ini ;
fi

# Http Server
echo "> Configure apache server"
./bin/.travis/configure_apache2.sh

# Create database
echo "> Create database and grant premissions to user 'ezp'"
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS behattestdb; GRANT ALL ON behattestdb.* TO ezp@localhost IDENTIFIED BY 'ezp';"
