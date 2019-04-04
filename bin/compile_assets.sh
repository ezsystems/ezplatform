#!/bin/bash

# Script to generate assets using `yarn encore`.
# It checks SYMFONY_ENV to ensure assets are generated for correct production/development environment.

if [ "${SYMFONY_ENV}" == "dev" ] ; then
    yarn encore dev
else
    yarn encore prod
fi
