#!/bin/bash
# This script gets run as part of the .platform.app.yaml deploy step
# On PE Cluster (usually just production) this should be setup by P.sh team as part of pre_start event

set -e

#date
echo "removing var/cache/${SYMFONY_ENV}/*.* to avoid Symfony container issues on interface changes"
rm -Rf var/cache/${SYMFONY_ENV}/*.*
#date
echo "clearing application cache"
bin/console cache:clear
#date
echo "done executing pre_start cache clear"
