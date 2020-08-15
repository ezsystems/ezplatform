#!/bin/sh

echo "> Prepare X and start Selenium"
export DISPLAY=:99.0
sh -e /etc/init.d/xvfb start
wget http://selenium-release.storage.googleapis.com/2.47/selenium-server-standalone-2.47.1.jar
java -jar selenium-server-standalone-2.47.1.jar -log /tmp/selenium.log &
cd -

# Give Selenium some time to start, otherwise tests will fail under high load on test servers
sleep 8
