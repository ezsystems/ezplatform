#!/bin/bash

# This script is meant to be reused from other repos that needs to run behat tests.
#
# Example use:
#
# before_install:
#  - git fetch --unshallow && git checkout -b tmp_travis_branch
#  - export BRANCH_BUILD_DIR=$TRAVIS_BUILD_DIR
#  - export TRAVIS_BUILD_DIR="$HOME/build/ezplatform"
#  - cd "$HOME/build"
#  - git clone --depth 1 --single-branch --branch master https://github.com/ezsystems/ezplatform.git
#  - cd ezplatform
#  - ./bin/.travis/setup_from_external_repo.sh $BRANCH_BUILD_DIR "ezsystems/demobundle:dev-tmp_travis_branch"

REPO_DIR=$1
COMPOSER_REQUIRE=${@:2}

./bin/.travis/prepare_system.sh
./bin/.travis/prepare_selenium2.sh

echo "> Modify composer.json to point to local checkout"
sed -i '$d' composer.json
echo ',    "repositories": [{"type":"git","url":"'$REPO_DIR'"}]}' >> composer.json

if [ -n "$COMPOSER_REQUIRE" ]; then
  echo "> Updating packages ($COMPOSER_REQUIRE)"
  composer require --no-update "$COMPOSER_REQUIRE"
fi

cat composer.json
./bin/.travis/prepare_ezpublish.sh

echo "> Warm up cache, using curl to make sure everything is warmed up, incl class, http & spi cache"
curl -sSLI "http://localhost"
