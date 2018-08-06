#! /bin/bash


echo $CRON_ONLY
echo $TRAVIS_CRON

if [ "$CRON_ONLY" -eq "1" ] && [ "$TRAVIS_CRON" != "CRON" ]; then 
 echo 'nie uruchom, exit'
fi
