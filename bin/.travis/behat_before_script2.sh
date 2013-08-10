#!/bin/sh

# Script to do tasks before script, step 2 of 2
## Step 2 runs finishing scripts, you can swap out a vendor for testing
## before this step, but if new vendor or vastly different some composer magic also needs to be done in between

composer run-script --dev post-install-cmd
php ezpublish/console --env=behat assetic:dump
php ezpublish/console --no-interaction --env=behat ezpublish:test:init_db
