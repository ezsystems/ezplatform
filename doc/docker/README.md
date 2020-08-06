# Docker blueprints

Unsupported docker building blocks used for some of our automated functional testing infrastructure at eZ, feel free to
copy it for own use or look to it for some recommended settings.

**NOTE**: If you are just looking to get easily up and running and developing with eZ Platform, rather look towards
community supported [eZ Launchpad](https://ezsystems.github.io/launchpad/) which is tailored for Project Development use cases. _If not, be
aware of the following limitations:_

> **WARNING, made mainly for automation:** The tools within this directory are meant for use for test automation, QA,
Support and demo use cases. And with time as a blueprint for how to best configure your own setup. You are free to use
and adopt this for your needs, and we more than welcome contributions to improve it.

> **WARNING, low performance on MacOS and Windows:** For reasons mentioned above, these tools are not
optimized for use as development environment with Mac or Windows, and are affected by known I/O performance issues caused
by Docker for Mac/Windows use of shared folders. This is a know issue and nothing we intend to add complexity to workaround here.

## Overview

This setup currently requires Docker Compose 1.14 and Docker 17.06 or higher. Defaults are set in `.env`, and
files to ignore are set in `.dockerignore`. By default `.env` specifies that dev setup is used.

_**NB:** For this and other reasons all docker-compose commands **must** be executed from root of your project directory._

#### Before you begin: Install Docker & Docker-Compose

Before jumping into steps below, make sure you have recent versions of [Docker & Docker-Compose](https://www.docker.com/)
installed on your machine.

*For Windows you'll also need to [install bash](https://msdn.microsoft.com/en-us/commandline/wsl/about), or adapt instructions below for Windows command line where needed.*


#### Concept: Docker Compose "Building blocks" for eZ Platform

The current Docker Compose files are made to be mixed and matched together for QA/Support use cases. Currently available:
- base-prod.yml _(required, always needs to be first, contains: db, web and app container)_
- base-dev.yml _(alternative to `base-prod.yml`, same applies here if used)_
- create-dataset.yml _(optional, to be used together with base-prod.yml in order to set up db and vardir)_
- demo.yml _(optional, to be used together with base-prod.yml in order to set up db and vardir)_
- dfs.yml _(optional, adds DFS cluster handler. Note that you need to run the migrate script manually, see below)_
- blackfire.yml _(optional, adds blackfire service and lets you trigger profiling against the setup)_
- redis.yml _(optional, adds redis service and appends config to app)_
- redis-session.yml _(optional, stores sessions in a separate redis instance)_
- varnish.yml _(optional, adds varnish service and appends config to app)_
- solr.yml _(optional, add solr service and configure app for it)_
- db-postgresql.yml _(optional, switches the DB engine to PostgreSQL - experimental)_
- selenium.yml _(optional, always needs to be last, adds selenium service and appends config to app)_
- multihost.yml _(optional, adds multihost config to app container network)_


These can be used with `-f` argument on docker-compose, like:
```bash
docker-compose -f doc/docker/base-prod.yml -f doc/docker/create-dataset.yml -f doc/docker/demo.yml -f doc/docker/redis.yml up -d --force-recreate
```

However below environment variable `COMPOSE_FILE` is used instead since this is also what is used to have a default in
`.env` file at root of the project.


## Project setup

### Demo "image" use

Using this approach, everything will run in containers and volumes. This means that if you for instance upload a image
using the eZ Platform backend, that image will land in a volume, not somewhere below web/var/ in your project directory.

From root of your projects clone of this distribution, [setup composer auth.json](#composer) and execute the following:
```sh
export COMPOSE_FILE=doc/docker/base-prod.yml:doc/docker/create-dataset.yml:doc/docker/demo.yml

# Optional step if you'd like to use blackfire with the setup, change <id> and <token> with your own values
#export COMPOSE_FILE=doc/docker/base-prod.yml:doc/docker/create-dataset.yml:doc/docker/demo.yml:doc/docker/blackfire.yml BLACKFIRE_SERVER_ID=<id> BLACKFIRE_SERVER_TOKEN=<token>

# First time: Install setup, and generate database dump:
docker-compose -f doc/docker/install-dependencies.yml -f doc/docker/install-database.yml up --abort-on-container-exit

# Optionally, build dbdump and vardir images.
# The dbdump image is created based on doc/docker/entrypoint/mysql/2_dump.sql which is created by above command
# The vardir image is created based on the content of web/var
# If you don't build these image explicitly, they will automaticly be builded later when running `docker-compose up`
docker-compose build dataset-vardir dataset-dbdump

# Boot up full setup:
docker-compose up -d --force-recreate
```

After some 5-10 seconds you should be able to browse the site on `localhost:8080` and the backend on `localhost:8080/admin`.

### Development "mount" use

Using this approach, your project directory will be bind mounted into the nginx and php containers. So if you change a
php file in for instance src/, that change will kick in automatically.

Warning: *Dev setup works a lot faster on Linux then on Windows/Mac where Docker uses virtual machines using shared folders
by default under the hood, which leads to much slower IO performance.*

From root of your projects clone of this distribution, [setup composer auth.json](#composer) and execute the following:
```sh
# Optional: If you use Docker Machine with NFS, you'll need to specify where project is, & give composer a valid directory.
#export COMPOSE_DIR=/data/SOURCES/MYPROJECTS/ezplatform/doc/docker COMPOSER_HOME=/tmp

# First time: Install setup, and generate database dump:
docker-compose -f doc/docker/install-dependencies.yml -f doc/docker/install-database.yml up --abort-on-container-exit

# Boot up full setup:
docker-compose up -d --force-recreate
```


After some 5-10 seconds you should be able to browse the site on `localhost:8080` and the backend on `localhost:8080/admin`.


_TIP: If you are seeing 500 errors, or in the case of `SYMFONY_ENV=dev` Database exceptions, then make sure to comment out `database_*` params in `app/config/parameters.yml` to make sure env variables are used correctly._

### Behat and Selenium use

*Docker-Compose setup for Behat use is provided and used internally to test eZ Platform, this can be combined with most
setups, here shown in combination with production setup which is what you'll typically need to test before pushing your
image to Docker Hub/Registry.*

From root of your projects clone of this distribution, [setup composer auth.json](#composer) and execute the following:
```sh
export COMPOSE_FILE=doc/docker/base-prod.yml:doc/docker/selenium.yml

# First time: Install setup, and generate database dump:
docker-compose -f doc/docker/install-dependencies.yml -f doc/docker/install-database.yml up --abort-on-container-exit

# Boot up full setup:
docker-compose up -d --force-recreate
```

*Last step is to execute behat scenarios using `app` container which now has access to web and selenium containers, example:*
```
docker-compose exec --user www-data app sh -c "php /scripts/wait_for_db.php; php bin/behat -vv --profile=rest --suite=fullJson --tags=~@broken"
```


*Tip: You can typically re run the install command to get back to a clean installation in between behat runs using:*
```
docker-compose exec --user www-data app composer ezplatform-install
```

### DFS

If you want to use the DFS cluster handler, you'll need to run the migration script manually, after starting the
containers ( run `docker-compose up -d --force-create` first).

The migration script will copy the binary files in web/var to the nfs mount point ( ./dfsdata ) and add the files'
metadata to the database. If your are going to run eZ Platform in a cluster you must then ensure ./dfsdata  is a mounted
nfs share on every node/app container.

```
# Enter the app container
docker-compose exec --user www-data app /bin/bash

# Inside app container
php app/console ezplatform:io:migrate-files --from=default,default --to=dfs,nfs --env=prod

```

Once this is done, you may delete web/var/* if you don't intendt to run the migration scripts ever again.

### Production use

#### Example: Building app with php image

In this example we'll build a app image which includes both php (php_fpm) and the eZ Platform application and run them
in a swarm cluster using docker stack.

Prerequisite:
- A running [swarm cluster](https://docs.docker.com/engine/swarm/swarm-tutorial/) ( a one-node cluster is sufficient for running this example )
- A running NFS server. How to configure a nfs server is distro dependent, but this [ubuntu guide](https://help.ubuntu.com/community/NFSv4Howto) might be of help
- A running [docker registry](https://docs.docker.com/registry/deploying/#managing-with-compose) (Only required if your swarm cluster has more than one node)

In this example we assume your swarm manager is named `swarmmanager` and that this hostname resolves on all swarm hosts. We also assume that the nfs server and docker registry are running on `swarmmanager`.

All the commands below should be executed on your `swarmmanager`

```sh
# If not already done, install setup, and generate database dump :
docker-compose -f doc/docker/install-dependencies.yml -f doc/docker/install-database.yml up --abort-on-container-exit

# Build docker_app and docker_web images ( php and nginx )
docker-compose -f doc/docker/base-prod.yml build --no-cache app web

# Build varnish image
docker-compose -f doc/docker/base-prod.yml -f doc/docker/varnish.yml build --no-cache varnish

# Create dataset images ( my-ez-app-dataset-dbdump and my-ez-app-dataset-vardir )
# The dataset images contains a dump of the database and a dump of the var/ files ( located in web/var )
docker-compose -f doc/docker/create-dataset.yml build --no-cache

# Tag the images
docker tag docker_dataset-dbdump swarmmanager:5000/my-ez-app/dataset-dbdump
docker tag docker_dataset-vardir swarmmanager:5000/my-ez-app/dataset-vardir
docker tag docker_web swarmmanager:5000/my-ez-app/web
docker tag docker_app swarmmanager:5000/my-ez-app/app
docker tag docker_varnish swarmmanager:5000/my-ez-app/varnish

# Upload the images to the registry ( only needed if your swarm cluster has more than one node)
docker push swarmmanager:5000/my-ez-app/dataset-dbdump
docker push swarmmanager:5000/my-ez-app/dataset-vardir
docker push swarmmanager:5000/my-ez-app/web
docker push swarmmanager:5000/my-ez-app/app
docker push swarmmanager:5000/my-ez-app/varnish

# In this example we run the database in a separate stack so that you may easily have multiple eZ Platform installations using the same database instance
docker stack deploy --compose-file doc/docker/db-stack.yml stack-db

# Now, wait a half a minute to ensure that the database is ready to accept incomming requests before continuing

# Now, load the database dump into the db and the var dir to the nfs server
docker-compose -f doc/docker/import-dataset.yml up

# Finally, create the eZ Platform stack
docker stack deploy --compose-file doc/docker/my-ez-app-stack.yml my-ez-app-stack

# Cleanup
# If you want to remove the stacks again:
docker stack rm my-ez-app-stack
sleep 15
docker stack rm stack-db
sleep 15
docker volume rm my-ez-app-stack_vardir
docker volume rm stack-db_mysql
```

#### Example: Separating app and php

In this alternative way of running eZ Platform, the eZ Platform code and PHP executables are separated in two different
images. The upside of this is that it gets easier to upgrade PHP ( or any other distro applications ) independently
of eZ Platform; simply just replace the PHP container with an updated one without having to rebuild the eZ Platform
image. The downside of this approach is that all eZ Platform code is copied to a volume so that it can be shared with
other containers. This means bigger disk space footprint and longer loading time of the containers.
It is also more complicated to make this approach work with docker stack so only a docker-compose example is provided.

```sh
export COMPOSE_FILE=doc/docker/base-prod.yml:doc/docker/create-dataset.yml:doc/docker/distribution.yml
# If not already done, install setup, and generate database dump :
docker-compose -f doc/docker/install-dependencies.yml -f doc/docker/install-database.yml up --abort-on-container-exit

# Build docker_app and docker_web images ( php and nginx )
# The docker_app image (which contain both php and eZ Platform) will be used as base image when creating the image which
# only contains the eZ Platform files.
docker-compose -f doc/docker/base-prod.yml build --no-cache app

# Optional, only build the images, do not create containers
docker-compose build --no-cache distribution

# Note that if you set the environment variable COMPOSE_PROJECT_NAME to a non-default value, you'll need to use set the
# build argument DISTRIBUTION_IMAGE when building the distribution image
docker-compose build --no-cache --build-arg DISTRIBUTION_IMAGE=customprojectname_app distribution

# Build the "distribution" and dataset images, then start the containers
docker-compose up -d
```

## Further info

### <a name="composer"></a>Configuring Composer

For composer to run correctly as part of the build process, you'll need to create a `auth.json` file in your project root with your github readonly token:

```sh
echo "{\"github-oauth\":{\"github.com\":\"<readonly-github-token>\"}}" > auth.json
# If you use eZ Enterprise software, also include your updates.ez.no auth token
echo "{\"github-oauth\":{\"github.com\":\"<readonly-github-token>\"},\"http-basic\":{\"updates.ez.no\": {\"username\":\"<installation-key>\",\"password\":\"<token-pasword>\",}}}" > auth.json
```

For further information on tokens for updates.ez.no, see [doc.ez.no](https://doc.ez.no/display/DEVELOPER/Using+Composer).



### Debugging

For checking logs from the containers themselves, use `docker-compose logs`. Here on `app` service, but can be omitted to get all:
```sh
docker-compose logs -t app
```


You can login to any of the services using `docker-compose exec`, here shown against `app` image and using `bash`:
```sh
docker-compose exec app /bin/bash
```

To display running services:
```sh
docker-compose ps
```

### Database dumps

Database dump is placed in `doc/docker/entrypoint/mysql/`, this folder is used my mysql/mariadb which will execute
everything inside the folder. This means there should only be data represent one install in the folder at any given time.


### Updating service images

To updated the used service images, you can run:
```sh
docker-compose pull --ignore-pull-failures
```

This assumed you either use `docker-compose -f` or have `COMPOSE_FILE` defined in cases where you use something else
then defaults in `.env`.

After this you can re run the production or dev steps to setup containers again with updated images.

### Cleanup

Once you are done with your setup, you can stop it, and remove the involved containers.
```sh
docker-compose down -v
```

And if you have defined any environment variables you can unset them using:
```sh
unset COMPOSE_FILE COMPOSE_DIR COMPOSER_HOME

# To unset blackfire variables
unset BLACKFIRE_SERVER_ID BLACKFIRE_SERVER_TOKEN
```
