#!/bin/bash

# This script provides setup steps needed to build eZ Platform docker containers ready to execute
# functional and acceptance (behat) tests.
#
# Example usage:
# $ ./bin/.travis/trusty/setup_ezplatform.sh "${COMPOSE_FILE}" "${INSTALL_TYPE}" ["${DEPENDENCY_PACKAGE_DIR}"]
#
# Arguments:
# - ${COMPOSE_FILE}           compose file(s) paths
# - ${INSTALL_TYPE}           optional, eZ Platform install type ("clean") will take from .env if not set
# - ${DEPENDENCY_PACKAGE_DIR} optional, directory containing existing eZ Platform dependency package

# Determine eZ Platform Build dir as relative to current script path
EZPLATFORM_BUILD_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/../../.." && pwd )"

# Source .env first to make sure we don't override any variables
. ${EZPLATFORM_BUILD_DIR}/.env

DEPENDENCY_PACKAGE_DIR=$3

if [[ -z "${1}" ]]; then
    COMPOSE_FILE=$COMPOSE_FILE
else
    COMPOSE_FILE=$1
fi

if [[ -z "${2}" ]]; then
    INSTALL_TYPE=$INSTALL_EZ_INSTALL_TYPE
else
    INSTALL_TYPE=$2
fi

if [[ -n "${DEPENDENCY_PACKAGE_DIR}" ]]; then
    # Get details about dependency package
    DEPENDENCY_PACKAGE_NAME=`php -r "echo json_decode(file_get_contents('${DEPENDENCY_PACKAGE_DIR}/composer.json'))->name;"`

    if [[ -z "${DEPENDENCY_PACKAGE_NAME}" ]]; then
        echo 'Missing composer package name of tested dependency' >&2
        exit 2
    fi
fi

echo '> Preparing eZ Platform container using the following setup:'
echo "- EZPLATFORM_BUILD_DIR=${EZPLATFORM_BUILD_DIR}"
echo "- COMPOSE_FILE=${COMPOSE_FILE}"
if [[ -n "${DEPENDENCY_PACKAGE_NAME}" ]]; then
    echo "- DEPENDENCY_PACKAGE_NAME=${DEPENDENCY_PACKAGE_NAME}"
fi

echo '> Remove XDebug PHP extension'
phpenv config-rm xdebug.ini

# Handle dependency if needed
if [[ -n "${DEPENDENCY_PACKAGE_NAME}" ]]; then
    # get dependency branch alias
    BRANCH_ALIAS=`php -r "echo json_decode(file_get_contents('${DEPENDENCY_PACKAGE_DIR}/composer.json'))->extra->{'branch-alias'}->{'dev-tmp_ci_branch'};"`
    if [[ $? -ne 0 || -z "${BRANCH_ALIAS}" ]]; then
        echo 'Failed to determine branch alias. Add extra.branch-alias.dev-tmp_ci_branch config key to your tested dependency composer.json' >&2
        exit 3
    fi

    # move dependency to directory available for docker volume
    BASE_PACKAGE_NAME=`basename ${DEPENDENCY_PACKAGE_NAME}`
    echo "> Move ${DEPENDENCY_PACKAGE_DIR} to ${EZPLATFORM_BUILD_DIR}/${BASE_PACKAGE_NAME}"
    mv ${DEPENDENCY_PACKAGE_DIR} ${EZPLATFORM_BUILD_DIR}/${BASE_PACKAGE_NAME}

    echo "> Create temporary branch in ${DEPENDENCY_PACKAGE_NAME}"
    cd ${EZPLATFORM_BUILD_DIR}/${BASE_PACKAGE_NAME}
    # reuse HEAD commit id for better knowledge about what got checked out
    TMP_TRAVIS_BRANCH=tmp_`git rev-parse --short HEAD`
    git checkout -b ${TMP_TRAVIS_BRANCH}

    # go back to previous directory
    cd -
fi

echo "> Start docker containers specified by ${COMPOSE_FILE}"
docker-compose up -d

if [[ -n "${DEPENDENCY_PACKAGE_NAME}" ]]; then
    # use local checkout path relative to docker volume
    echo "> Make composer use tested dependency local checkout ${TMP_TRAVIS_BRANCH} of ${BASE_PACKAGE_NAME}"
    docker-compose exec app sh -c "composer config repositories.localDependency git /var/www/${BASE_PACKAGE_NAME}"

    echo "> Require ${DEPENDENCY_PACKAGE_NAME}:dev-${TMP_TRAVIS_BRANCH} as ${BRANCH_ALIAS}"
    if ! docker-compose exec app sh -c "composer require --no-update '${DEPENDENCY_PACKAGE_NAME}:dev-${TMP_TRAVIS_BRANCH} as ${BRANCH_ALIAS}'"; then
        echo 'Failed requiring dependency' >&2
        exit 3
    fi
fi

echo '> Run composer install inside docker app container'
docker-compose exec app sh -c 'composer install --no-suggest --no-progress --no-interaction --prefer-dist --optimize-autoloader'

# for behat builds to work
echo '> Change ownership of files inside docker container'
docker-compose exec app sh -c 'chown -R www-data:www-data /var/www'

echo '> Install data'
docker-compose exec --user www-data app sh -c "php /scripts/wait_for_db.php; php bin/console ezplatform:install ${INSTALL_TYPE}"

echo '> Done, ready to run tests'
