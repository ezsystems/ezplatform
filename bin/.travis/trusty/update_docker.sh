#!/usr/bin/env sh

# Update package info and selectively update docker-engine (and keep old travis specific config file)
#disabled to save startup time as last travis image already have docker v1.12.0
#sudo apt-get update
#sudo apt-get --reinstall -y -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" install docker-engine
#docker -v

# If we need to pin it to a given version:
# sudo apt-get --reinstall -y [...] install docker-engine=1.11.0-0~jessie
# http://apt.dockerproject.org/repo/dists/debian-jessie/main/binary-amd64/Packages


#DOCKER_COMPOSE_VERSION="1.8.0"
#echo "\nUpdating Docker Compose to ${DOCKER_COMPOSE_VERSION}"
#sudo rm -f /usr/local/bin/docker-compose
#curl -L https://github.com/docker/compose/releases/download/${DOCKER_COMPOSE_VERSION}/docker-compose-`uname -s`-`uname -m` > docker-compose
#chmod +x docker-compose
#sudo mv docker-compose /usr/local/bin
