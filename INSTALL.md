# Installation instructions

## Terms for future reference:
  * `/<root-dir>/`: The filesystem path where eZ Platform is installed in.
    Examples: `/home/myuser/www/` or `/var/sites/<project-name>/`
  * cli: command line interface. For *Linux/BSD/OS X* specific commands, use of `bash` or similar is assumed.

## Prerequisite

  These instructions assume you have technical knowledge and have already installed PHP, web server &
  *a database server* needed for this software. For further information on requirements [see online doc](https://doc.ez.no/display/EZP/Requirements)

  **Before you start**:
  - Create Database: Installation will ask you for credentials/details for which database to use
    *Note: Right now installer only supports MySQL, Postgres support should be (re)added in one of the upcoming releases.*
  - Set php.ini memory_limit=256M before running commands below
  - *Optional:* You can also setup Solr to be used by eZ Platform and take note of the url it is accessible on

## Install

1. **Install/Extract eZ Platform**<a name="install-1-extract"></a>:

    There are two ways to install eZ Platform described below, what is common is that you should make sure
    relevant settings are generated into `app/config/parameters.yml` as a result of this step.

    `parameters.yml` contains settings for your database, mail system, and optionally [Solr](http://lucene.apache.org/solr/)
    if `search_engine` is configured as `solr`, as opposed to default `legacy` *(a limited database powered search engine)*.

    A. **Extract archive** (tar/zip) *from http://share.ez.no/downloads/downloads*

       Extract the eZ Platform 15.01 (or higher) archive to a directory, then execute post install scripts:

       *Note: The post install scripts will ask you to fill in some settings, including database settings.*

       ```bash
       $ cd /<directory>/
       $ curl -sS https://getcomposer.org/installer | php
       $ php -d memory_limit=-1 composer.phar run-script post-install-cmd
       ```


    B. **Install via Composer**

     You can get eZ Platform using composer with the following commands:

     *Note: composer will take its time to download all libraries and when done you will be asked to fill in some settings, including database settings.*

       ```bash
       $ curl -sS https://getcomposer.org/installer | php
       $ php -d memory_limit=-1 composer.phar create-project --no-dev ezsystems/ezplatform <directory> [<version>]
       $ cd /<directory>/
       ```

     Options:
       - `<version>`: Optional, *if omitted you'll get latest stable*, examples for specifying:
        - `~1.0.0`: Example of getting latests 1.0.x release, recommended
        - `v1.0.0-beta5`: example of picking a specific tag
        - `dev-master`: to get current development version (pre release) `master` branch
       - For core development: Add '--prefer-source' to get full git clones, and remove '--no-dev' to get things like phpunit and behat installed.
       - Further reading: https://getcomposer.org/doc/03-cli.md#create-project

2. *Only for *NIX users* **Setup folder rights**<a name="install-2-folder-rights"></a>:

       Like most things, [Symfony documentation](http://symfony.com/doc/current/book/installation.html#checking-symfony-application-configuration-and-setup)
       applies here, meaning `app/cache` and `app/logs` need to be writable by cli and web server user. Furthermore, future
       files and directories created by these two users will need to inherit those access rights. *For security reasons,
       there is no need for web server to have access to write to other directories.*

       Change `www-data` to your web server user:

       A. **Using ACL on a *Linux/BSD* system that supports chmod +a**

       ```bash
       $ rm -rf app/cache/* app/logs/*
       $ sudo chmod +a "www-data allow delete,write,append,file_inherit,directory_inherit" \
         app/cache app/logs web
       $ sudo chmod +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" \
         app/cache app/logs web
       ```

       B. **Using ACL on a *Linux/BSD* system that does not support chmod +a**

       Some systems don't support chmod +a, but do support another utility called setfacl. You may need to enable ACL support on your partition and install setfacl before using it (as is the case with Ubuntu), like so:

       ```bash
       $ sudo setfacl -R -m u:www-data:rwx -m u:`whoami`:rwx \
         app/cache app/logs web
       $ sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx \
         app/cache app/logs web
       ```

       C. **Using chown on *Linux/BSD/OS X* systems that don't support ACL**

       Some systems don't support ACL at all. You will need to set your web server's user as the owner of the required directories.

       ```bash
       $ sudo chown -R www-data:www-data app/cache app/logs web
       $ sudo find {app/{cache,logs},web} -type d | xargs sudo chmod -R 775
       $ sudo find {app/{cache,logs},web} -type f | xargs sudo chmod -R 664
       ```

       D. **Using chmod on a *Linux/BSD/OS X* system where you can't change owner**

       If you can't use ACL and aren't allowed to change owner, you can use chmod, making the files writable by everybody. Note that this method really isn't recommended as it allows any user to do anything.

       ```bash
       $ sudo find {app/{cache,logs},web} -type d | xargs sudo chmod -R 777
       $ sudo find {app/{cache,logs},web} -type f | xargs sudo chmod -R 666
       ```

       When using chmod, note that newly created files (such as cache) owned by the web server's user may have different/restrictive permissions.
       In this case, it may be required to change the umask so that the cache and log directories will be group-writable or world-writable (`umask(0002)` or `umask(0000)` respectively).

       It may also possible to add the group ownership inheritance flag so new files inherit the current group, and use `775`/`664` in the command lines above instead of world-writable:
       ```bash
       $ sudo chmod g+s {app/{cache,logs},web}
       ```

       E. **Setup folder rights on Windows**

       For your choice of web server you'll need to make sure web server user has read access to `<root-dir>`, and
       write access to the following directories:
       - app/cache
       - app/logs


3. **Configure a VirtualHost**<a name="install-3-vhost"></a>:

    A virtual host setup is the recommended, most secure setup of eZ Publish.
    General virtual host setup template for Apache and Nginx can be found in [doc/ folder](doc/).


4. **Run installation command**<a name="install-4-db-setup"></a>:

    You may now complete the eZ Platform installation with ezplatform:install command, example of use:

    ```bash
    $ php -d memory_limit=-1 app/console ezplatform:install --env prod clean
    ```

    **Note**: Password for the generated `admin` user is `publish`, this name and password is needed when you would like to login to backend Platform UI. Future versions will prompt you for a unique password during installation.

You can now point your browser to the installation and browse the site. To access the Platform UI backend, use the `/ez` URL.
