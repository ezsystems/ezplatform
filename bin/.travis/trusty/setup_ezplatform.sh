#!/bin/bash

# This script provides setup steps needed to build eZ Platform docker containers ready to execute
# functional and acceptance (behat) tests.
#
# Example usage:
# $ ./bin/.travis/trusty/setup_ezplatform.sh "${COMPOSE_FILE}" "${INSTALL_TYPE}" ["${DEPENDENCY_PACKAGE_DIR}"]
#
# Arguments:
# - ${COMPOSE_FILE}           compose file(s) paths
# - ${INSTALL_TYPE}           eZ Platform install type ("clean")
# - ${DEPENDENCY_PACKAGE_DIR} optional, directory containing existing eZ Platform dependency package

COMPOSE_FILE=$1
INSTALL_TYPE=$2
DEPENDENCY_PACKAGE_DIR=$3

# Determine eZ Platform Build dir as relative to current script path
EZPLATFORM_BUILD_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/../../.." && pwd )"

if [[ -z "${COMPOSE_FILE}" ]]; then
    echo 'Argument 1 should contain path to compose file(s). None given.' >&2
    exit 1
fi

if [[ -z "${INSTALL_TYPE}" ]]; then
    echo 'Argument 2 should contain eZ Platform install type. None given' >&2
    exit 2
fi

if [[ -n "${DEPENDENCY_PACKAGE_DIR}" ]]; then
    # Get details about dependency package
    DEPENDENCY_PACKAGE_NAME=`php -r "echo json_decode(file_get_contents('${DEPENDENCY_PACKAGE_DIR}/composer.json'))->name;"`
    DEPENDENCY_BRANCH_ALIAS=`php -r "echo json_decode(file_get_contents('${DEPENDENCY_PACKAGE_DIR}/composer.json'))->extra->{'branch-alias'}->{'dev-master'};"`

    if [[ -z "${DEPENDENCY_PACKAGE_NAME}" ]]; then
        echo 'Missing composer package name of tested dependency' >&2
        exit 3
    fi

    if [[ -z "${DEPENDENCY_BRANCH_ALIAS}" ]]; then
        echo 'Missing composer branch alias of tested dependency' >&2
        exit 4
    fi

    # Use package name as directory name for better readability
    TESTED_DEPENDENCY_DIR=`basename ${DEPENDENCY_PACKAGE_NAME}`
    # Move package into location accessible by docker container
    echo "> Move '${DEPENDENCY_PACKAGE_DIR}' to '${TESTED_DEPENDENCY_DIR}'"
    mv ${DEPENDENCY_PACKAGE_DIR} ${TESTED_DEPENDENCY_DIR}

    echo "> Modify eZ Platform composer.json to point to local checkout of ${DEPENDENCY_PACKAGE_NAME}"
    composer config repositories.tested_dependency path ${TESTED_DEPENDENCY_DIR}

    # require dependency using branch-alias version so local checkout gets symlinked
    COMPOSER_REQUIRE="${DEPENDENCY_PACKAGE_NAME}:${DEPENDENCY_BRANCH_ALIAS}"
    echo "> Requiring packages: $COMPOSER_REQUIRE"
    composer require --no-update "${COMPOSER_REQUIRE}"
fi

echo '> Preparing eZ Platform container using the following setup:'
echo "- EZPLATFORM_BUILD_DIR=${EZPLATFORM_BUILD_DIR}"
echo "- COMPOSE_FILE=${COMPOSE_FILE}"
if [[ -n "${DEPENDENCY_PACKAGE_DIR}" ]]; then
    echo "- DEPENDENCY_PACKAGE_NAME=${DEPENDENCY_PACKAGE_NAME}"
    echo "- DEPENDENCY_PACKAGE_DIR=${DEPENDENCY_PACKAGE_DIR}"
    echo "- DEPENDENCY_BRANCH_ALIAS=${DEPENDENCY_BRANCH_ALIAS}"
fi

echo '> Remove XDebug PHP extension'
phpenv config-rm xdebug.ini

echo "> Start docker containers specified by ${COMPOSE_FILE}"
docker-compose up -d
docker-compose exec app sh -c 'chown -R www-data:www-data /var/www'

echo '> Run composer install inside docker app container'
docker-compose exec --user www-data app sh -c 'COMPOSER_HOME=$HOME/.composer composer install --no-suggest --no-progress --no-interaction --prefer-dist --optimize-autoloader'

echo '> Install data'
docker-compose exec --user www-data app sh -c "php /scripts/wait_for_db.php; php bin/console ezplatform:install ${INSTALL_TYPE}"

echo '> Done, ready to run tests'
