# Installation instructions

NOTE: *For simplified installation, consider using [eZ Launchpad](https://ezsystems.github.io/launchpad/) which takes care about the whole setup for you.*

These installation instructions are kept current in the online docs here:
https://doc.ezplatform.com/en/latest/getting_started/install_using_composer/

## Prerequisite

  These instructions assume you have technical knowledge and have already installed PHP, web server & *a database server* needed for this software. For further information [on requirements see online doc](https://doc.ezplatform.com/en/latest/getting_started/requirements_and_system_configuration/).

# Installation Using Composer

## Get Composer

If you don't have it already, install Composer, the command-line package manager for PHP. You'll have to have a copy of Git installed on your machine. The following command uses PHP to download and run the Composer installer, and should be entered on your terminal and executed by pressing Return or Enter:

``` bash
php -r "readfile('https://getcomposer.org/installer');" | php
```

For further information about Composer usage see [Using Composer](about_composer.md).

## eZ Platform Installation

The commands below assume you have Composer installed globally, a copy of git on your system, and your **MySQL/MariaDB server *already set up* with a database**. Once you've got all the required PHP extensions installed, you can get eZ Platform up and running with the following commands:

``` bash
composer create-project --no-dev --keep-vcs ezsystems/ezplatform ezplatform
cd ezplatform

#at the end of the install process, you will be told to perform the following commands:
export SYMFONY_ENV="prod"
php bin/console ezplatform:install clean
php bin/console assetic:dump
```

!!! note

    For more information about the availables options with Composer commands, see [the Composer documentation](https://getcomposer.org/doc/03-cli.md).
Then [you can start eZ Platform!](https://doc.ezplatform.com/en/latest/getting_started/starting_ez_platform/)

### Installing another distribution

eZ Platform exists in several distributions, listed in [Installation eZ Platform](install_ez_platform.md), some with their own installer as shown in the example below. To install the Enterprise Edition you need an eZ Enterprise subscription and have to [set up Composer for that](about_composer.md).

**eZ Platform Enterprise Edition**

``` bash
composer create-project --no-dev --keep-vcs ezsystems/ezplatform-ee
cd ezplatform-ee

# Options are listed on php bin/console ezplatform:install -h
php bin/console ezplatform:install studio-clean
```

!!! enterprise

    ###### Enable Date-based Publisher

    To enable delayed publishing of Content using the Date-based publisher, see [the manual installation guide](install_manually.md#enable-date-based-publisher_1).

### Installing another version

The instructions above show how to install the latest stable version, however with Composer you can specify the version and stability as well if you want to install something else. Using `composer create-project -h` you can see how you can specify another version:

> create-project \[options\] \[--\] \[&lt;package&gt;\] \[&lt;directory&gt;\] \[&lt;version&gt;\]
>
>  
>
> Arguments:
>
>   &lt;package&gt;                            Package name to be installed
>
>   &lt;directory&gt;                            Directory where the files should be created
>
>   &lt;version&gt;                              Version, will default to latest

Versions [can be expressed in many ways in Composer,](https://getcomposer.org/doc/articles/versions.md) but the ones we recommend are:

-   Exact git tag: `v1.3.1`
-   Tilde for locking down the minor version: `~1.3.0`
    -   Equals: 1.3.\* 
-   Caret for allowing all versions within a major: `^1.3.0`
    -   Equals: 1.\* &lt;= 1.3.0

What was described above concerns stable releases, however [Composer lets you specify stability in many ways](https://getcomposer.org/doc/articles/versions.md#stability), mainly:

-   Exact git tag: `v1.4.0-beta1`
-   Stability flag on a given version: `1.4.0@beta`
    -   Equals: versions of 1.4.0 in stability order of: beta, rc, stable
    -   This can also be combined with tilde and caret to match ranges of unstable releases
-   Stability flag while omitting version: '`@alpha` equals latest available alpha release

Example:

``` bash
composer create-project --no-dev --keep-vcs ezsystems/ezplatform-demo ezplatform @beta
cd ezplatform

php bin/console ezplatform:install demo
```

