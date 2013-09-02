#!/bin/sh

# Script to do tasks before install, can install system packages / software
# See http://about.travis-ci.org/docs/user/build-configuration/

wget -nv -O sahi_20130429.zip "http://downloads.sourceforge.net/project/sahi/sahi-v44/sahi_20130429.zip?r=http%3A%2F%2Fsourceforge.net%2Fprojects%2Fsahi%2Ffiles%2Fsahi-v44%2F&ts=1376728867&use_mirror=garr"
unzip -o sahi_20130429.zip -d  ~
rm sahi_20130429.zip
sudo chmod +x ~/sahi/bin/sahi.sh

sudo apt-get update
sudo apt-get install -q -y --force-yes apache2 libapache2-mod-fastcgi
