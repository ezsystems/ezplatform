# Installation using Docker

#### Intro

This setup requires Docker Compose 1.7 or higher, and Docker 1.10 or higher. Defaults are set in `.env`, and
files to ignore are set in `.dockerignore`. By default a production image is built and setup for use.

#### Installing Docker

Before jumping into steps below for either production / demo or development use, make sure you have recent versions of
[Docker & Docker-Compose](https://www.docker.com/) installed on your machine.

*For Windows you'll also need to [install bash](https://msdn.microsoft.com/en-us/commandline/wsl/about), or adapt instructions below for Windows command line where needed.*


## Production / Demo use

From root of your projects clone of this distribution, [setup composer auth.json](#composer) and execute the following:
```sh
# Optional step if you'd like to use blackfire with the setup, change <id> and <token> with your own values
#export COMPOSE_FILE=doc/docker-compose/prod.yml:doc/docker-compose/blackfire.yml BLACKFIRE_SERVER_ID=<id> BLACKFIRE_SERVER_TOKEN=<token>

docker-compose up -d --force-recreate --build
```

*Last step is to execute the eZ Platform install.*
```sh
docker-compose exec --user www-data app /bin/sh -c "php /scripts/wait_for_db.php; php app/console ezplatform:install clean"
```

At this point you should be able to browse the site on `localhost:8080` and the backend UI on `localhost:8080/ez`.

## Development use

Warning: *Dev setup works a lot faster on Linux then on Windows/Mac where Docker uses virtual machines using shared folders
by default under the hood, which leads to much slower IO performance.*

From root of your projects clone of this distribution, [setup composer auth.json](#composer) and execute the following:
```sh
export COMPOSE_FILE=doc/docker-compose/prod.yml:doc/docker-compose/dev.yml SYMFONY_ENV=dev SYMFONY_DEBUG=1

# Optional: If you use Docker Machine with NFS, you'll need to specify where project is, & give composer a valid directory.
#export COMPOSE_DIR=/data/SOURCES/MYPROJECTS/ezplatform/doc/docker-compose COMPOSER_HOME=/tmp

docker-compose -f doc/docker-compose/install.yml up install
```

*Lastly we execute docker-compose to get containers running, and then eZ Platform install script.*
```sh
docker-compose up -d --force-recreate --no-build
docker-compose exec --user www-data app /bin/sh -c "php /scripts/wait_for_db.php; php app/console ezplatform:install clean"
```


At this point, you should be able to browse the site on `localhost:8080` and the backend UI on `localhost:8080/ez`.


## Behat use

From root of your projects clone of this distribution, [setup composer auth.json](#composer) and execute the following:
```sh
export COMPOSE_FILE=doc/docker-compose/prod.yml:doc/docker-compose/selenium.yml

docker-compose up -d --force-recreate --build
```

*Next step is to execute the eZ Platform install.*
```sh
docker-compose exec --user www-data app /bin/sh -c "php /scripts/wait_for_db.php; php app/console ezplatform:install clean"
```

*Last step is to execute behat scenarios using `behatphpcli` container which has access to web and selenium containers, example:*
```
docker-compose run -u www-data --rm behatphpcli bin/behat -vv --profile=rest --suite=fullJson --tags=~@broken
```

*Tip: You can typically re run the install command to get back to a clean installation in between behat runs, without recreating the full docker setup.*


## Other Tasks

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

### Updating service images

To updated the used service images, you can run:
```sh
docker-compose pull --ignore-pull-failures
```

This assumed you either use `docker-compose -f` or have `COMPOSE_FILE` defined in cases where you use something else
then defaults in `.env`.

After this you can re run the production or dev steps to setup containers again with updated images.

### Uninstalling

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