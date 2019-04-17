#!/bin/bash

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
if (( $(echo "$dc < 1.23" |bc -l) )); then
    DOCKER_COMPOSE_VERSION="1.23.2"
    echo "Updating Docker Compose from ${dc} (${dc_full}) to ${DOCKER_COMPOSE_VERSION}"
    sudo rm -f /usr/local/bin/docker-compose
    curl -L https://github.com/docker/compose/releases/download/${DOCKER_COMPOSE_VERSION}/docker-compose-`uname -s`-`uname -m` > docker-compose
    chmod +x docker-compose
    sudo mv docker-compose /usr/local/bin
else
    echo "Skip updating Docker Compose ${dc} (${dc_full})"
fi
