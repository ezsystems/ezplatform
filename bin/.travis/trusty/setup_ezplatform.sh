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

echo "> Start docker containers specified by ${COMPOSE_FILE}"
docker-compose up -d
docker-compose exec app sh -c 'chown -R www-data:www-data /var/www'

echo '> Run composer install inside docker app container'
docker-compose exec --user www-data app sh -c 'COMPOSER_HOME=$HOME/.composer composer install --no-suggest --no-progress --no-interaction --prefer-dist --optimize-autoloader'

# Handle dependency if needed
if [[ -n "${DEPENDENCY_PACKAGE_NAME}" ]]; then
    # check if dependency exists for current meta-package version
    if [[ ! -d "./vendor/${DEPENDENCY_PACKAGE_NAME}" ]]; then
        echo "Testing dependency failed: package ${DEPENDENCY_PACKAGE_NAME} does not exist" >&2
        exit 3
    fi

    echo "> Overwrite ./vendor/${DEPENDENCY_PACKAGE_NAME} with ${DEPENDENCY_PACKAGE_DIR}"
    if ! (sudo rm -rf "./vendor/${DEPENDENCY_PACKAGE_NAME}" && sudo mv ${DEPENDENCY_PACKAGE_DIR} "./vendor/${DEPENDENCY_PACKAGE_NAME}"); then
        echo 'Overwrite failed' >&2
        exit 4
    fi

    echo '> Clear Symfony cache inside docker app container'
    docker-compose exec --user www-data app sh -c 'php ./bin/console cache:clear --no-warmup && php ./bin/console cache:warmup'
fi

echo '> Install data'
docker-compose exec --user www-data app sh -c "php /scripts/wait_for_db.php; php bin/console ezplatform:install ${INSTALL_TYPE}"

echo '> Done, ready to run tests'
