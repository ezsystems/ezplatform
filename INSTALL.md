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
composer create-project --keep-vcs ezsystems/ezplatform ezplatform
cd ezplatform
```

At this point you need to [set up directory permissions](#setting-up-directory-permissions).

Next, use the installation command:

``` bash
php bin/console ezplatform:install clean
```

Finally, dump assets:

``` bash
php bin/console assetic:dump
```

#### Installing another version

The instructions above show how to install the latest stable version, however with Composer you can specify the exact version and stability level you want to install.

Versions [can be expressed in many ways in Composer](https://getcomposer.org/doc/articles/versions.md), but the ones we recommend are:

-   Exact git tag: `v1.3.1`
-   Tilde for locking down the minor version: `~1.3.0`
    -   Equals: `1.3.*``
-   Caret for allowing all versions within a major: `^1.3.0`
    -   Equals: `1.* <= 1.3.0`

The above concerns stable releases, but [Composer lets you specify stability in many ways](https://getcomposer.org/doc/articles/versions.md#stability), mainly:

-   Exact git tag: `v1.4.0-beta1`
-   Stability flag on a given version: `1.4.0@beta`
    -   Equals: versions of 1.4.0 in stability order of: beta, rc, stable
    -   This can also be combined with tilde and caret to match ranges of unstable releases
-   Stability flag while omitting version: '`@alpha` equals latest available alpha release

Example:

``` bash
composer create-project --keep-vcs ezsystems/ezplatform ezplatform @beta
cd ezplatform
php bin/console ezplatform:install clean
```

## Setting up directory permissions

Directories `var`, `web/var` need to be writable by CLI and web server user
(see [Symfony documentation](http://symfony.com/doc/3.4/setup/file_permissions.html))

Furthermore, future files and directories created by these two users will need to inherit those permissions.

*For security reasons, in production there is no need for web server to have write permission to other directories.*

For development setup you may change your web server config to use the same user as the owner of a directory.
What follows below is mainly for production setup, and like Symfony we first and foremost recommend using an ACL.

### Using ACL on a Linux/BSD system that supports chmod +a

Some systems, primarily Mac OS X, support setting ACL using a `+a` flag on `chmod`. The example uses a command to
try to determine your web server user and set it as `HTTPDUSER`, alternatively change to your actual web server
user if non standard:

```bash
rm -rf var/cache/* var/logs/* var/sessions/*
HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)
sudo chmod +a "$HTTPDUSER allow delete,write,append,file_inherit,directory_inherit" var web/var
sudo chmod +a "$(whoami) allow delete,write,append,file_inherit,directory_inherit" var web/var
```

### Using ACL on a Linux/BSD system that does not support chmod +a

Some systems don't support `chmod +a`, but do support another utility called setfacl. You may need to
[enable ACL support](https://help.ubuntu.com/community/FilePermissionsACLs) on your partition and install setfacl
before using it *(as is the case with Ubuntu)*. The example below uses a command to try to determine
your web server user and set it as `HTTPDUSER`, alternatively change to your actual web user if non standard:

```bash
HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)
# if this does not work, try adding '-n' option
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX var web/var
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX var web/var
```

### Using chown on Linux/BSD/OS X systems that don't support ACL

Some systems don't support ACL at all. You will need to set your web server's user as the owner of the required
directories, in this setup further Symfony console commands against installation should use the web server user
as well to avoid new files being created using another user. The example uses a command to try to determine your
web server user and set it as `HTTPDUSER`, alternatively change to your actual web server user if non standard:

```bash
HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)
sudo chown -R "$HTTPDUSER":"$HTTPDUSER" var web/var
sudo find web/var var -type d | xargs sudo chmod -R 775
sudo find web/var var -type f | xargs sudo chmod -R 664
```

### Using chmod on a Linux/BSD/OS X system where you can't change owner

If you can't use ACL and aren't allowed to change owner, you can use chmod, making the files writable by everybody.
**Note that this method really isn't recommended as it allows any user to do anything.**

```bash
sudo find web/var var -type d | xargs sudo chmod -R 777
sudo find web/var var -type f | xargs sudo chmod -R 666
```

When using `chmod`, note that newly created files (such as cache) owned by the web server's user may have different/restrictive permissions.
In this case, it may be required to change the umask so that the cache and log directories will be group-writable or world-writable (`umask(0002)` or `umask(0000)` respectively).

It may also be possible to add the group ownership inheritance flag so new files inherit the current group, and use `775`/`664` in the command lines above instead of world-writable:

```bash
sudo chmod g+s web/var var
```

### Setting up directory permissions on Windows

For your choice of web server you'll need to make sure web server user has read access to `<root-dir>`, and
write access to the following directories:
- `web/var`
- `var`
