#!/usr/bin/env sh
# This script is run as part of the .platform.app.yaml deployment step
# On PE Cluster (usually just production) this should be setup by platform.sh team as part of pre_start event

set -e

#date
echo "removing var/cache/${SYMFONY_ENV-dev}/*.* to avoid Symfony container issues on interface changes"
rm -Rf var/cache/${SYMFONY_ENV-dev}/*.*
#date
echo "clearing application cache"
php bin/console cache:clear
#date
echo "done executing pre_start cache clear"
