#! /usr/bin/env bash

# Install any build dependencies needed for curl
sudo apt-get build-dep curl

# Get latest (as of Feb 25, 2016) libcurl
mkdir ~/curl
cd ~/curl
wget http://curl.haxx.se/download/curl-7.50.2.tar.bz2
tar -xvjf curl-7.50.2.tar.bz2
cd curl-7.50.2

# The usual steps for building an app from source
# ./configure
# ./make
# sudo make install
./configure
make
sudo make install

# Resolve any issues of C-level lib
# location caches ("shared library cache")
sudo ldconfig