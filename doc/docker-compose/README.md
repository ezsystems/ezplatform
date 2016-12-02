# Using Docker Tools for your project

> **Beta**: Instructions and Tools *(Docker images, Dockerfile, docker-compose files, scripts, ..)* described on this page
 is currently in Beta for community testing & contribution, and might change without notice.
 See [online Docker Tools documentation](https://doc.ez.no/display/DEVELOPER/Docker+Tools) for known issues and further information.


**"Project"**: *This page describes usage for your custom project code, for just launching a stock eZ Platform demo see [online Docker Tools documentation](https://doc.ez.no/display/DEVELOPER/Docker+Tools)*.


## Overview

This setup requires Docker Compose 1.7 or higher, and Docker 1.10 or higher. Defaults are set in `.env`, and
files to ignore are set in `.dockerignore`. By default `.env` specifies that production image is built and setup for use.
_**NB:** For this and other reasons all docker-compose commands **must** be executed from root of your project directory._

#### Before you begin: Install Docker & Docker-Compose

Before jumping into steps below, make sure you have recent versions of [Docker & Docker-Compose](https://www.docker.com/)
installed on your machine.

*For Windows you'll also need to [install bash](https://msdn.microsoft.com/en-us/commandline/wsl/about), or adapt instructions below for Windows command line where needed.*


#### Concept: Docker Compose "Building blocks" for eZ Platform

The current Docker Compose files are made to be mixed and matched togtehr as you'd like. Currently available:
- base-prod.yml _(required, always needs to be first, contains: db, web and app container)_
- base-dev.yml _(alternative to `base-prod.yml`, same applies here if used)_
- redis.yml _(optional, adds redis service and appends config to app)_
- solr.yml _(optional, work in progress config to add solr service and configure app for it, for testing only)_
- blackfire.yml _(optional, adds blackfire service and lets you trigger profiling against the setup)_
- selenium.yml _(optional, always needs to be last, adds selenium service and appends config to app)_


These can be used with `-f` argument on docker-compose, like:
```bash
docker-compose -f doc/docker-compose/base-prod.yml -f doc/docker-compose/redis.yml up -d --force-recreate
```

However below environment variable `COMPOSE_FILE` is used instead since this is also what is used to have a default in
`.env` file at root of the project.


## Project setup

### Production / Demo "image" use

From root of your projects clone of this distribution, [setup composer auth.json](#composer) and execute the following:
```sh
# Optional step if you'd like to use blackfire with the setup, change <id> and <token> with your own values
#export COMPOSE_FILE=doc/docker-compose/base-prod.yml:doc/docker-compose/blackfire.yml BLACKFIRE_SERVER_ID=<id> BLACKFIRE_SERVER_TOKEN=<token>

# First time: Install setup, and generate database dump:
docker-compose -f doc/docker-compose/install.yml up --abort-on-container-exit

# Boot up full setup:
docker-compose up -d --force-recreate
```

After some 5-10 seconds you should be able to browse the site on `localhost:8080` and the backend on `localhost:8080/ez`.

### Development "mount" use


Warning: *Dev setup works a lot faster on Linux then on Windows/Mac where Docker uses virtual machines using shared folders
by default under the hood, which leads to much slower IO performance.*

From root of your projects clone of this distribution, [setup composer auth.json](#composer) and execute the following:
```sh
export COMPOSE_FILE=doc/docker-compose/base-dev.yml SYMFONY_ENV=dev SYMFONY_DEBUG=1

# Optional: If you use Docker Machine with NFS, you'll need to specify where project is, & give composer a valid directory.
#export COMPOSE_DIR=/data/SOURCES/MYPROJECTS/ezplatform/doc/docker-compose COMPOSER_HOME=/tmp

# First time: Install setup, and generate database dump:
docker-compose -f doc/docker-compose/install.yml up --abort-on-container-exit

# Boot up full setup:
docker-compose up -d --force-recreate
```


After some 5-10 seconds you should be able to browse the site on `localhost:8080` and the backend on `localhost:8080/ez`.


### Behat and Selenium use

*Docker-Compose setup for Behat use is provided and used internally to test eZ Platform, this can be combined with most
setups, here shown in combination with production setup which is what you1'll typically need to test before pushing your
image to Docker Hub/Registry.*

From root of your projects clone of this distribution, [setup composer auth.json](#composer) and execute the following:
```sh
export COMPOSE_FILE=doc/docker-compose/base-prod.yml:doc/docker-compose/selenium.yml

# First time: Install setup, and generate database dump:
docker-compose -f doc/docker-compose/install.yml up --abort-on-container-exit

# Boot up full setup:
docker-compose up -d --force-recreate
```

*Last step is to execute behat scenarios using `app` container which now has access to web and selenium containers, example:*
```
docker-compose exec --user www-data app sh -c "php /scripts/wait_for_db.php; php bin/behat -vv --profile=rest --suite=fullJson --tags=~@broken"
```


*Tip: You can typically re run the install command to get back to a clean installation in between behat runs using:*
```
docker-compose exec --user www-data app app/console ezplatform:install clean
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

Database dump is placed in `doc/docker-compose/entrypoint/mysql/`, this folder is used my mysql/mariadb which will execute
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
unset COMPOSE_FILE SYMFONY_ENV SYMFONY_DEBUG COMPOSE_DIR COMPOSER_HOME

# To unset blackfire variables
unset BLACKFIRE_SERVER_ID BLACKFIRE_SERVER_TOKEN
```
