#!/usr/bin/env bash

# Due to EZP-28816: console cache:clear doesn't remove cache from Redis, so we need to do it manually.

set -e

. ../../.env

function clearRedisCache
{
    REDISHOST=`php -r '$r=getenv("PLATFORM_RELATIONSHIPS");$r=json_decode(base64_decode($r), true);echo($r["rediscache"][0]["host"]);'`
    REDISPORT=`php -r '$r=getenv("PLATFORM_RELATIONSHIPS");$r=json_decode(base64_decode($r), true);echo($r["rediscache"][0]["port"]);'`

    redis-cli -h $REDISHOST -p $REDISPORT FLUSHALL
}

# When deploying changes to existing cluster, clear all cache now that we have shared mounts available
if [ "$SYMFONY_ENV" != "prod" ] ; then
    # Clear class cache before we boot up symfony in case of interface changes on classes cached
    rm -Rf ../../app/cache/$SYMFONY_ENV/*.*
    php ../../app/console cache:clear
elif [ -d "../../app/cache/prod/$PLATFORM_TREE_ID" ] ; then
    # Clear cache on re-deploy when the folder exits, move folder so post_deploy can cleanup
    mv -f ../../app/cache/prod/$PLATFORM_TREE_ID ../../app/cache/prod/old_deploy
fi

clearRedisCache

trap - EXIT
