#!/bin/bash

SCRIPT_DIR=`dirname $0`

# Update Docker, if needed
d_full=`docker version --format '{{.Server.Version}}'`
d=`echo $d_full | ( IFS="." ; read a b c && echo $a.$b)`
if (( $(echo "$d < 18.06" |bc -l) )); then
    echo "Updating Docker from ${d} (${d_full}) to newest community edition"
    # Update package info and selectively update docker-engine (and keep old travis specific config file)
    sudo apt-get update
    sudo apt-get --reinstall -y -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" install docker-ce
    docker -v
else
    echo "Skip updating Docker ${d} (${d_full})"
fi

# If we need to pin it to a given version:
# sudo apt-get --reinstall -y [...] install docker-engine=1.11.0-0~jessie
# http://apt.dockerproject.org/repo/dists/debian-jessie/main/binary-amd64/Packages

# Update Docker Compose, if needed
dc_full=`docker-compose version --short`
dc=`echo $dc_full | ( IFS="." ; read a b c && echo $a.$b)`
if (( ! $(echo "$dc < 1.23" |bc -l) )); then
    echo "Skip updating Docker Compose ${dc} (${dc_full})"
    exit 0
fi

DOCKER_COMPOSE_VERSION="1.23.2"
echo "Updating Docker Compose from ${dc} (${dc_full}) to ${DOCKER_COMPOSE_VERSION}"
GITHUB_TOKEN=$(cat ${SCRIPT_DIR}/../composer-auth.json | jq -r '.["github-oauth"]["github.com"]')
PLATFORM=docker-compose-`uname -s`-`uname -m`

DOCKER_COMPOSE_DOWNLOAD_URL=$(curl -H "Authorization: token ${GITHUB_TOKEN}" https://api.github.com/repos/docker/compose/releases/tags/${DOCKER_COMPOSE_VERSION} | jq -r --arg p "$PLATFORM" '.assets[] | select(.name == $p) | .url')
echo "Downloading docker-compose from ${DOCKER_COMPOSE_DOWNLOAD_URL}"
curl -L -H "Authorization: token ${GITHUB_TOKEN}" -H "Accept: application/octet-stream" $DOCKER_COMPOSE_DOWNLOAD_URL > docker-compose-dl

FILE_TYPE=$(file -b --mime-type docker-compose-dl | sed 's|/.*||')
if [[ $FILE_TYPE == "application" ]]; then
    sudo rm -f /usr/local/bin/docker-compose
    chmod +x docker-compose-dl
    cat docker-compose-dl
    sudo mv docker-compose-dl /usr/local/bin/docker-compose
else
    echo "Error when downloading docker-compose"
    exit 1
fi
