#!/usr/bin/env sh

if [ ! -d app ] ; then
    echo ABORT: No app/ folder found, you need to run this script from your application root
    exit 1
fi

if [ -d .git ] ; then
    FOLDER_SIZE=`du -ms .git | cut -f1`
    if [ "$FOLDER_SIZE" -gt "30" ]; then
        echo "ABORT: Detected .git/ folder with more then 30mb in it (${FOLDER_SIZE}mb), this can slow down build a lot if to big"
        exit 1
    fi
fi

if [ -d vendor/ezsystems/ezpublish-kernel/.git ] ; then
    echo "ABORT: Detected vendor/ezsystems/ezpublish-kernel/.git folder, this can slow down build a lot, remember to use --prefer-dist"
    exit 1
fi

if [ -d ezpublish_legacy/.git ] ; then
    echo ABORT: Detected  ezpublish_legacy/.git folder, this can be up to several giagabytes that you don\'t want to send to docker deamon.
    exit 1
fi

if [ ! -f composer.lock ] ; then
    echo Did not detect composer.lock file. Ok but recommended, especially for tags.
fi

# Need to specify volume name
if [ "$1" = "" ] ; then
    echo ABORT: Missing argument VOLUME_NAME, run as: ./bin/.docker/build.sh ezsystems/ezplatform_volume:latest
    exit 1
fi

VOLUME_NAME=$1

if [ ! -d vendor ] ; then
    echo "No vendor directory found, using php image to install vendors."
    if [ "$COMPOSER_HOME" = "" ]; then
        COMPOSER_HOME=~/.composer
    fi

    if [ ! -d $COMPOSER_HOME ] ; then
        echo ABORT: Could not find a composer directory on host for auth and cache reuse, tried using ${COMPOSER_HOME}
        exit 1
    fi

    # Set SYMFONY_ENV to prod in php image to dump right assets, also make sure to reuse composer setitngs/cache
    docker run --rm \
        -v `pwd`:/var/www \
        -v  $COMPOSER_HOME:/home/ez/.composer \
        -w /var/www \
        -e SYMFONY_ENV=prod \
        ezsystems/php:7.0-v0.4 \
        /bin/sh -c "composer install --no-progress --no-interaction --prefer-dist"
fi

docker build --no-cache -f bin/.docker/Dockerfile -t $VOLUME_NAME .
