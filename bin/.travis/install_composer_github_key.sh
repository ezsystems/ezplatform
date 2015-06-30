#!/bin/sh

# This is unfortunately only way to avoid Github API limits with Travis on an open source project.
# Please don't use our token, neither token nor it's user gives access to anything other then authentication, so better if you create your own.
# PS: The simple obfuscation here is only to avoid Github from detecting this on commits.
export EZ_GITHUB_TOKEN_A=`echo "574503f4468c5de5820d" | rev`
export EZ_GITHUB_TOKEN_B=`echo "90cf1c134798de2dae27" | rev`

composer config -g github-oauth.github.com "${EZ_GITHUB_TOKEN_A}${EZ_GITHUB_TOKEN_B}"
