#!/usr/bin/env bash

until $(curl --output /dev/null --silent --head --fail http://web:80); do
    echo "Waiting for web."
    sleep 2
done

exec "$@"
