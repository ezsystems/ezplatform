#!/bin/bash
# Script used to make eZ Platform/Enterprise meta tags
#
# Only aims to:
# - prepare code for project use
# - check some things to validate if we are ready for making tag
# - make the tag
#
# Arguments:
# - tag: v3.4.2
# - composer args: arguments to pass to composer update (optional)

# Vendors we watch for stability (and potentially more).
PACKAGE_WATCH_REGEX='/^(doctrine|ezsystems|silversolutions|symfony)\//';

UNSTAGED_CHANGES=`git status | grep 'Changes not staged for commit'`
if (( ${#UNSTAGED_CHANGES} > 0 )); then
  echo -e "\033[31m You have unstaged changes. Please commit or stash them. \033[0m"
  exit
fi

set -e

TAG=$1
if [[ $TAG =~ ^v[0-9]+(\.[0-9]+){2,3}(-[a-z]+[0-9]*)?$ ]]; then
  echo -e "\033[36m Start work on making tag $TAG \033[0m"
else
  echo -e "\033[31m Tag argument did not look correct, should be v1.2.33 or v2.3.4-beta1 \033[0m"
  exit
fi
TMP_BRANCH="tmp_release_$TAG"

shift
COMPOSER_ARGS="$@"

CURRENT_BRANCH_LINE=`git branch | grep '*'`
#* 2.5|master
if [[ $CURRENT_BRANCH_LINE =~ ^\*[[:space:]]([^[:space:]]+)$ ]]; then
  CURRENT_BRANCH=${BASH_REMATCH[1]}
#* (detached from v2.5.0)
elif [[ $CURRENT_BRANCH_LINE =~ ^\*[[:space:]]\(detached[[:space:]]from[[:space:]]([^[:space:]]+)\)$ ]]; then
  CURRENT_BRANCH=${BASH_REMATCH[1]}
#* (HEAD detached at v2.5.0)
elif [[ $CURRENT_BRANCH_LINE =~ ^\*[[:space:]]\(HEAD[[:space:]]detached[[:space:]]at[[:space:]]([^[:space:]]+)\)$ ]]; then
  CURRENT_BRANCH=${BASH_REMATCH[1]}
#* (no branch)
else
  echo -e "\033[31m Can't detect current branch/tag. \033[0m"
  exit
fi

devPackageScanPhp=$(cat <<HEREDOC
\$hash = json_decode(file_get_contents("composer.json"), true);
foreach (\$hash["require"] as \$package => \$version) {
    if (preg_match('$PACKAGE_WATCH_REGEX', \$package) === 1 && is_int(strpos(\$version, "@dev"))) {
        echo "  \$package \$version\n";
    }
}
HEREDOC)

devPackages=$(php -r "$devPackageScanPhp")
if (( ${#devPackages} > 0 )); then
  echo -e "\033[31m There are unwanted dev packages in composer.json: \033[0m"
  printf "$devPackages\n"
  exit
fi

# TODO: Add help text, display help on errors

# After this we want to be able to cleanup things on exit (clean and error)
clean_up () {
    ARG=$?
    git reset -q --hard HEAD
    git checkout -q $CURRENT_BRANCH
    git branch -q -D $TMP_BRANCH
    exit $ARG
}
trap clean_up EXIT



# Let's start!

git checkout -b $TMP_BRANCH

echo -e "\033[36m Comment out *.lock files in .gitignore \033[0m"
perl -pi -e 's/^(.*)\.lock$/#$1.lock/g' .gitignore

minimumPHP=$(php -r '$hash = json_decode(file_get_contents("composer.json"), true); $php = str_replace(["^", "~"], "", $hash["require"]["php"]); echo explode("|", $php)[0];')
echo -e "\033[36m Set minimum php version in composer.json (temporary to get vendor capable of working with it) to $minimumPHP \033[0m"
composer config platform.php "$minimumPHP"

echo -e "\033[36m Update composer packages to generate lock files \033[0m"
php -d memory_limit=-1 `which composer` update --no-interaction --prefer-dist $COMPOSER_ARGS

echo -e "\033[36m Revert composer.json minimum php version changes, and update composer lock file hash to avoid warning \033[0m"
git checkout composer.json
php -d memory_limit=-1 `which composer` update --lock --no-scripts --no-interaction --prefer-dist $COMPOSER_ARGS

echo -e "\033[36m Add changes, commit and tag \033[0m"
git add -f *.lock .gitignore
git commit -m "Configure $TAG for release"
git tag -f $TAG

echo -e "\033[36m \nReady to push the tag once you have checked it to be correct, once ready:\033[0m"
echo -e "\033[33m git push <remote> $TAG\n \033[0m"

echo -e "\033[2m After this it's possible to: \033[0m"
echo -e "\033[2m - Make release notes: https://github.com/yannickroger/release-notes-generator \033[0m"
echo -e "\033[2m - Checkout tag and make archive for upload: ./bin/.ci/prepare_archive.sh \n \033[0m"
