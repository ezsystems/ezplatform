#!/bin/sh

mkdir -p ~/.composer
echo '{ "config": { "github-oauth": { "github.com": "cf20c86050d2c206b34d1fa8958dca40bfe08afd" } } }' > ~/.composer/config.json
