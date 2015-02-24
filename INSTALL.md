# Installation instructions

  These are instructions for installing via GIT (development version), look in INSTALL_ARCHIVE.md for instructions on how to install a eZ Platform build/archive.

## Paths for future reference
  * `/<root-dir>/`: The filesystem path where eZ Platform is installed in

    Examples: `/home/myuser/www/` or `/var/sites/<project-name>/`

  * `/<root-dir>/web/`: A directory meant to be the **DocumentRoot** of the eZ Platform installation (`<root-dir>` is not supposed to be _readable_ from web server perspective)

    Examples: `/home/myuser/www/web/` or `/var/sites/<project-name>/web/`

## Prerequisite

  These instructions assume you have strong technical knowledge and have already installed PHP, web server & a database server with a corresponding clean database needed for this software.
  For further information on requirements see: https://doc.ez.no/display/EZP/Requirements

## Install

1. **Get eZ Platform**:

    A. **Archive** (tar/zip)

       Extract the eZ Platform 2015.01(or higher) archive to a directory, then execute post install scripts:

       *Note: The post install scripts will ask you to fill in some settings, including database settings.*

       ```bash
       cd /<directory>/
       curl -s http://getcomposer.org/installer | php
       php -d memory_limit=-1 composer.phar run-script post-install-cmd
       ```


    B. **Composer**

     You can get eZ Platform using composer with the following commands:

     *Note: composer will take its time to download all libraries and when done you will be asked to fill in some settings, including database settings.*

       ```bash
       curl -s http://getcomposer.org/installer | php
       php -d memory_limit=-1 composer.phar create-project --no-dev --prefer-dist ezsystems/ezpublish-community <directory> <version>
       cd /<directory>/
       ```

     Options:
       - `<version>`: `dev-master` to get current development version (pre release), `v2015.01` to pick a specific release, otherwise skip it to get latest stable release.
       - For core development change '--prefer-dist' to '--prefer-source' to get full git clones, and remove '--no-dev' to get things like phpunit and behat installed.

2. *Only for *NIX users* **Setup folder rights**:

       One common issue is that the `ezpublish/cache`, `ezpublish/logs` and `ezpublish/config` directories **must be writable both by the web server and the command line user**.
       If your web server user is different from your command line user, you can run the following commands just once in your project to ensure that permissions will be set up properly.

       Change `www-data` to your web server user:

       A. **Using ACL on a system that supports chmod +a**

       ```bash
       $ rm -rf ezpublish/cache/* ezpublish/logs/* ezpublish/sessions/*
       $ sudo chmod +a "www-data allow delete,write,append,file_inherit,directory_inherit" \
         ezpublish/{cache,logs,config,sessions} web
       $ sudo chmod +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" \
         ezpublish/{cache,logs,config,sessions} web
       ```

       B. **Using ACL on a system that does not support chmod +a**

       Some systems don't support chmod +a, but do support another utility called setfacl. You may need to enable ACL support on your partition and install setfacl before using it (as is the case with Ubuntu), like so:

       ```bash
       $ sudo setfacl -R -m u:www-data:rwx -m u:www-data:rwx \
         ezpublish/{cache,logs,config,sessions} web
       $ sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx \
         ezpublish/{cache,logs,config,sessions} web
       ```

       C. **Using chown on systems that don't support ACL**

       Some systems don't support ACL at all. You will either need to set your web server's user as the owner of the required directories.

       ```bash
       $ sudo chown -R www-data:www-data ezpublish/{cache,logs,config,sessions} web
       $ sudo find {ezpublish/{cache,logs,config,sessions},web} -type d | sudo xargs chmod -R 775
       $ sudo find {ezpublish/{cache,logs,config},web} -type f | sudo xargs chmod -R 664
       ```

       D. **Using chmod**

       If you can't use ACL and aren't allowed to change owner, you can use chmod, making the files writable by everybody. Note that this method really isn't recommended as it allows any user to do anything.

       ```bash
       $ sudo find {ezpublish/{cache,logs,config,sessions},web} -type d | sudo xargs chmod -R 777
       $ sudo find {ezpublish/{cache,logs,config,sessions},web} -type f | sudo xargs chmod -R 666
       ```

3. *Optional* **Configure a VirtualHost**:

    See: https://confluence.ez.no/display/EZP/Virtual+host+setup


4. **Run installation command**:

    You may now complete the eZ Platform installation with ezplatform:install command, example of use:

    ```bash
    $ php ezpublish/console ezplatform:install --env prod demo-clean
    ```

You can now point your browser to the installation and browse the site. To access the Platform UI backend, use the `/shell` URL.