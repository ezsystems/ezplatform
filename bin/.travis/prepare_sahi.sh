#!/bin/sh

# get and install sahi
echo "> Install Sahi"
wget -nv -O sahi_20130429.zip "http://downloads.sourceforge.net/project/sahi/sahi-v44/sahi_20130429.zip?r=http%3A%2F%2Fsourceforge.net%2Fprojects%2Fsahi%2Ffiles%2Fsahi-v44%2F&ts=1376728867&use_mirror=garr"
unzip -o sahi_20130429.zip -d  ~
rm sahi_20130429.zip
sudo chmod +x ~/sahi/bin/sahi.sh

# X & Sahi
echo "> Prepare X and start Sahi"
export DISPLAY=:99.0
sh -e /etc/init.d/xvfb start
cp -f bin/.travis/sahi/browser_types.xml-dist ~/sahi/userdata/config/browser_types.xml
cd ~/sahi/bin
sh -e ./sahi.sh &
cd -

# Give Sahi some time to start, otherwise tests will fail if high load on test servers
sleep 8
