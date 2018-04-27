#!/usr/bin/env bash

# Due to EZP-28816: console cache:clear doesn't remove cache from Redis, so we need to do it manually.

set -e

. ../../.env

function clearRedisCache
{
    REDISHOST=`php -r '$r=getenv("PLATFORM_RELATIONSHIPS");$r=json_decode(base64_decode($r), true);echo($r["rediscache"][0]["host"]);'`
    REDISPORT=`php -r '$r=getenv("PLATFORM_RELATIONSHIPS");$r=json_decode(base64_decode($r), true);echo($r["rediscache"][0]["port"]);'`

    echo "FLUSHALL" | redis-cli -h $REDISHOST -p $REDISPORT
}

clearRedisCache

trap - EXIT
