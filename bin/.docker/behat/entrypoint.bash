#!/usr/bin/env bash

while [ -e ${CONTAINER_PROJECT_DIR}/.installinprogress ]
do
    echo "Waiting the end of the install."
    sleep 1
done

# Install the Vhost
cd ${CONTAINER_PROJECT_DIR}

cp -f behat.yml.dist behat.yml;
sed -i 's@localhost:4444@selenium:4444@' behat.yml;
sed -i 's@localhost@web@' behat.yml


exec "$@"
