#!/bin/bash

cp -i .env.dist .env
docker-compose -f doc/docker-compose/install.yml up --abort-on-container-exit
