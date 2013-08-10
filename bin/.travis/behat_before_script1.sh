#!/bin/sh

# Script to do tasks before script, step 1 of 2
## Step 1 install vendors + db + legacy + apache + sahi, but does not run scripts
## So you can swap out a vendor for testing between step 1 and 2

mysql -e "CREATE DATABASE IF NOT EXISTS behattestdb;" -uroot
composer install --dev --prefer-dist --no-scripts
./bin/.travis/prepare_legacy.sh

# Http Server
./bin/.travis/configure_apache2.sh

# X & Sahi
export DISPLAY=:99.0
sh -e /etc/init.d/xvfb start
cp -f bin/.travis/sahi/browser_types.xml-dist ~/sahi/userdata/config/browser_types.xml
cd ~/sahi/bin
sh -e ./sahi.sh &
cd -

# Give Sahi some time to start
sleep 4
