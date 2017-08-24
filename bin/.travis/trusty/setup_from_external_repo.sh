#!/bin/bash

# This script is meant to be reused from other repos that needs to run behat tests.
#
# It assumes you have already checked pout ezplatform (to get access to this script) and
# moved original package into tmp_travis_folder under ezplatform folder so it is accessible
# from inside Docker images.
#
## Example use:
#
# env:
#  - COMPOSE_FILE="doc/docker/prod.yml:doc/docker/selenium.yml"
#
# before_install:
#  - git fetch --unshallow && git checkout -b tmp_travis_branch
#  - export BRANCH_BUILD_DIR=$TRAVIS_BUILD_DIR TRAVIS_BUILD_DIR="$HOME/build/ezplatform"
#  - cd "$HOME/build"
#  - git clone --depth 1 --single-branch --branch master https://github.com/ezsystems/ezplatform.git
#  - cd ezplatform
#  - ./bin/.travis/trusty/setup_from_external_repo.sh $BRANCH_BUILD_DIR "ezsystems/demobundle:dev-tmp_travis_branch"
#
# script: docker-compose run -u www-data --rm behatphpcli bin/behat --profile=rest --suite=fullJson --tags=~@broken

REPO_DIR=$1
COMPOSER_REQUIRE=${@:2}

if [ "$COMPOSE_FILE" = "" ] ; then
    echo "No COMPOSE_FILE defined, exiting "
    exit 1
fi

echo "> Move '$REPO_DIR' to 'tmp_travis_folder'"
mv $REPO_DIR tmp_travis_folder
ls -al tmp_travis_folder
ls -al .

./bin/.travis/trusty/update_docker.sh

echo "> Modify composer.json to point to local checkout"
composer config repositories.tmp_travis_folder git ${HOME}/build/ezplatform/tmp_travis_folder


# Setup symlink for doc/docker-compose folder for compatibility with older package branches using this
ln -s docker doc/docker-compose

if [ "$RUN_INSTALL" = "1" ] ; then
  # TODO: avoid using composer on host so image don't need to be PHP image, needed atm as .
  # TODO: dockerignore or something strips info needed for composer to be able to find tmp_travis_branch
  if [ ! -f auth.json ]; then
    cp bin/.travis/composer-auth.json auth.json
  fi
  echo 'memory_limit = -1' >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini
  phpenv config-rm xdebug.ini
  if [ -n "$COMPOSER_REQUIRE" ] ; then
    # TODO: avoid using composer on host so image don't need to be PHP image
    echo "> Updating packages ($COMPOSER_REQUIRE)"
    composer require --no-update "$COMPOSER_REQUIRE"
    cat composer.json
  fi
  echo "> Run composer install"
  composer install --no-progress --no-interaction --prefer-dist --optimize-autoloader
  mkdir -p web/var
  rm -Rf app/logs/* app/cache/*/*
  sudo chown -R www-data:www-data app/cache app/logs web/var
  find app/cache app/logs web/var -type d | xargs chmod -R 775
  find app/cache app/logs web/var -type f | xargs chmod -R 664
  # Do NOT use this for your prod setup, this is done like this for behat
  sudo chown -R www-data:www-data app/config src
  #docker-compose -f doc/docker/install.yml up --abort-on-container-exit
fi

INSTALL_EZ_INSTALL_TYPE=${INSTALL_EZ_INSTALL_TYPE:-clean}

echo "> Start containers and install data"
docker-compose up -d
docker-compose exec --user www-data app sh -c "php /scripts/wait_for_db.php; php app/console ezplatform:install $INSTALL_EZ_INSTALL_TYPE"

echo "> Done, ready to run behatphpcli container"
