#!/usr/bin/env bash

###--- Vars

PORT_PREFIX=${PORT_PREFIX:="13"}
ROOT_PATH=${ROOT_PATH:="../../"}
BEHAT=${BEHAT:=""}

###---

f_compose ()
{

    # Handle Docker Machine
    if [ "$DOCKER_MACHINE_HOST" != "" ]; then
        docker-machine env $DOCKER_MACHINE_HOST
        eval "$(docker-machine env $DOCKER_MACHINE_HOST)"
        COMPOSER_HOME=/data/DOCKER_SOURCES/.composer
        # we have to trust the folder here.
        # @todo: Improve that
    else
        # On the host
        if [ "$COMPOSER_HOME" = "" ]; then
            COMPOSER_HOME=~/.composer
        fi
        if [ ! -d $COMPOSER_HOME ] ; then
            COMPOSER_HOME=/root/.composer
            exit 1
        fi
    fi
    cd bin/.docker/

    COMPOSE_BEHAT_ARGS=""
    if [ $BEHAT != "" ]; then
        COMPOSE_BEHAT_ARGS="-f docker-compose.behat.yml"
    fi
    COMPOSER_HOME=$COMPOSER_HOME ROOT_PATH=$ROOT_PATH PORT_PREFIX=$PORT_PREFIX docker-compose -p ezplatform -f docker-compose.yml $COMPOSE_BEHAT_ARGS $@
}


f_compose $1 $2 $3 $4 $5 $6 $7 $8 $9


