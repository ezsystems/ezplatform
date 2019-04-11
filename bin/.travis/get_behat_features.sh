#!/bin/bash
# Fastest option 'list-features' gives us the list of all features from given context in random order, which are later
# run in this order in few threads and dynamically distributed between these threads. That gives us different test build
# times each build, often non optimal. To make this optimal we sort features by the number of scenarios in them
# (ascending because Fastest reverse the queue order, and we want this queue to run descending) and run them in that order,
# to minimize final time gap between the threads.

bin/behat --suite=$1 --list-scenarios | awk '{ gsub(/:[0-9]+/,"",$1); print $1 }' | uniq -c | sort | awk '{ print $2 }'
