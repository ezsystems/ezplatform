#!/bin/bash

# This script provides setup steps needed to build eZ Platform docker containers ready to execute
# functional and acceptance (behat) tests.
#
# Example usage:
# $ ./bin/.travis/trusty/setup_ezplatform.sh "${COMPOSE_FILE}" "${INSTALL_TYPE}" ["${DEPENDENCY_PACKAGE_DIR}"]
#
# Arguments:
# - ${COMPOSE_FILE}           compose file(s) paths
# - ${INSTALL_TYPE}           *Not in use*
# - ${DEPENDENCY_PACKAGE_DIR} optional, directory containing existing eZ Platform dependency package

# Determine eZ Platform Build dir as relative to current script path
EZPLATFORM_BUILD_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/../../.." && pwd )"

DEPENDENCY_PACKAGE_DIR=$3

if [[ -z "${1}" ]]; then
    # If not set, read default from .env file
    export $(grep "COMPOSE_FILE" ${EZPLATFORM_BUILD_DIR}/.env)
else
    export COMPOSE_FILE=$1
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
    cp -R ${DEPENDENCY_PACKAGE_DIR} ${EZPLATFORM_BUILD_DIR}/${BASE_PACKAGE_NAME}
    cd ${EZPLATFORM_BUILD_DIR}/${BASE_PACKAGE_NAME}

    # perform full checkout to allow using as local Composer depenency
    git fetch --unshallow

    echo "> Create temporary branch in ${DEPENDENCY_PACKAGE_NAME}"
    # reuse HEAD commit id for better knowledge about what got checked out
    TMP_TRAVIS_BRANCH=tmp_`git rev-parse --short HEAD`
    git checkout -b ${TMP_TRAVIS_BRANCH}

    # go back to previous directory
    cd -

    # use local checkout path relative to docker volume
    # create the directory for non-container commands to pass
    if [ ! -d /var/www/${BASE_PACKAGE_NAME} ]; then
        sudo mkdir -p /var/www/${BASE_PACKAGE_NAME}
    fi
    echo "> Make composer use tested dependency local checkout ${TMP_TRAVIS_BRANCH} of ${BASE_PACKAGE_NAME}"
    REPOSITORY_PROPERTIES=$( jq -n \
                  --arg basePackageName "/var/www/$BASE_PACKAGE_NAME" \
                  '{"type": "path", "url": $basePackageName, "options": { "symlink": false }}' )
    composer config repositories.localDependency "$REPOSITORY_PROPERTIES"

    echo "> Require ${DEPENDENCY_PACKAGE_NAME}:dev-${TMP_TRAVIS_BRANCH} as ${BRANCH_ALIAS}"
    if ! composer require --no-update "${DEPENDENCY_PACKAGE_NAME}:dev-${TMP_TRAVIS_BRANCH} as ${BRANCH_ALIAS}"; then
        echo 'Failed requiring dependency' >&2
        exit 3
    fi
fi

if [[ -n "${DOCKER_PASSWORD}" ]]; then
    echo "> Set up Docker credentials"
    echo ${DOCKER_PASSWORD} | docker login -u ${DOCKER_USERNAME} --password-stdin
fi

# Copy .env file to the directory where Docker Compose looks for it to avoid specyfing it directly everywhere
cp .env doc/docker

echo "> Install DB and dependencies"
docker-compose -f doc/docker/install-dependencies.yml up --abort-on-container-exit

echo "> Start docker containers specified by ${COMPOSE_FILE}"
docker-compose up -d

# for behat builds to work
echo '> Change ownership of files inside docker container'
docker-compose exec -T app sh -c 'chown -R www-data:www-data /var/www'

echo '> Install data'
docker-compose exec -T --user www-data app sh -c "php /scripts/wait_for_db.php; composer --no-interaction ezplatform-install"

echo '> Done, ready to run tests'
