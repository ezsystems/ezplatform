#!/bin/sh

echo "> Prepare X and start Selenium"
export DISPLAY=:99.0
sh -e /etc/init.d/xvfb start
wget http://selenium-release.storage.googleapis.com/2.48/selenium-server-standalone-2.48.2.jar
java -jar selenium-server-standalone-2.48.2.jar > /dev/null 2>&1 &
cd -

# Give Selenium some time to start, otherwise tests will fail under high load on test servers
sleep 8
