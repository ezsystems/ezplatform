#!/bin/sh

# get selenium
wget http://selenium.googlecode.com/files/selenium-server-standalone-2.35.0.jar

# prepare X
export DISPLAY=:99.0
sh -e /etc/init.d/xvfb start

# run selenium2
java -jar selenium-server-standalone-2.35.0.jar > /dev/null &

# Give Selenium2 some time to start
sleep 5
