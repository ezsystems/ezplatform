# Installation

NOTE: *For simplified installation, consider using [eZ Launchpad](https://ezsystems.github.io/launchpad/) which takes care of the whole setup for you.*

These installation instructions are kept current [in the online docs](https://doc.ezplatform.com/en/latest/getting_started/install_using_composer/).

## Prerequisites

These instructions assume you have already installed PHP, a web server and a database server needed for eZ Platform. For further information [on requirements see online doc](https://doc.ezplatform.com/en/latest/getting_started/requirements_and_system_configuration/).

## Installation

### Get Composer

If you don't have it already, install Composer, the command-line package manager for PHP. You need a copy of git installed on your machine. The following command uses PHP to download and run the Composer installer:

``` bash
php -r "readfile('https://getcomposer.org/installer');" | php
```

For further information about Composer usage see the [Using Composer](https://doc.ezplatform.com/en/latest/getting_started/about_composer/) section.

### Install eZ Platform

The commands below assume you have Composer installed globally, a copy of git on your system, and your **MySQL/MariaDB server already set up with a database**. Once you've got all the required PHP extensions installed, you can get eZ Platform up and running with the following commands:

``` bash
composer create-project --keep-vcs ezsystems/ezplatform ezplatform ^2
cd ezplatform
```

During the installation process you will be asked to input things like database host name, login, password, etc.
They will be placed in `<ezplatform>/app/config/parameters.yml`.

Next you will receive instructions on how to install data into the database, and how to run a simplified dev server using the `server:run` command.

For a more complete and better performing setup using Apache or nginx, read up on how to [install eZ Platform manually](https://doc.ezplatform.com/en/latest/getting_started/install_manually/).

#### Installing another version

The instructions above show how to install the latest stable version, however with Composer you can [specify the exact version and stability level you want to install](https://doc.ezplatform.com/en/latest/getting_started/install_using_composer/#installing-another-version).
