#!/usr/bin/env bash

while [ -e ${CONTAINER_PROJECT_DIR}/.installinprogress ]
do
    echo "Waiting the end of the install."
    sleep 1
done

# Install the Vhost
cd ${CONTAINER_PROJECT_DIR}

PORT=80 HOST_LIST=_ SYMFONY_ENV=dev  ./bin/vhost.sh --template-file=doc/nginx/vhost.template > /etc/nginx/conf.d/default.conf
PORT=81 HOST_LIST=_ SYMFONY_ENV=prod SYMFONY_HTTP_CACHE=0  ./bin/vhost.sh --template-file=doc/nginx/vhost.template > /etc/nginx/conf.d/default_prod.conf

exec "$@"
