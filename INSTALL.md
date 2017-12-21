# Installation instructions

NOTE: *For simplified installation, consider using [eZ Launchpad](https://ezsystems.github.io/launchpad/) which takes care about the whole setup for you.*

These installation instructions are kept current in the online docs here:
https://doc.ezplatform.com/en/latest/getting_started/install_using_composer/


## Terms for future reference:
  * `/<root-dir>/`: The filesystem path where eZ Platform is installed in.
    Examples: `/home/myuser/www/` or `/var/sites/<project-name>/`
  * cli: command line interface. For *Linux/BSD/OS X* specific commands, use of `bash` or similar is assumed.

## Prerequisite

  These instructions assume you have technical knowledge and have already installed PHP, web server &
  *a database server* needed for this software. For further information on requirements [see online doc](https://doc.ezplatform.com/en/latest/getting_started/requirements_and_system_configuration/)

  **Before you start**:
  - Create Database: Installation will ask you for credentials/details for which database to use, example with mysql:
    `CREATE DATABASE <database> CHARACTER SET utf8;` *Note: Right now installer only supports MySQL and MariaDB, Postgres support will be (re)added in one of the upcoming releases.*
  - Set php.ini memory_limit=256M before running commands below
  - *Optional:* You can also setup Solr to be used by eZ Platform and take note of the url it is accessible on

## Install

1. **Install/Extract eZ Platform**<a name="install-1-extract"></a>:

    There are two ways to install eZ Platform described below, what is common is that you should make sure
    relevant settings are generated into `app/config/parameters.yml` as a result of this step.

    `parameters.yml` contains settings for your database, mail system, and so on.
     _Once installed, for additional settings you may configure here, see also `app/config/default_parameters.yml`._


    A. **Extract archive** (tar/zip)

       Download archive from [ezplatform.com](https://ezplatform.com/#download), __not from GitHub__ *(those are for Composer)*.
       Extract the eZ Platform v2.x archive to a directory, then execute post install scripts:

       *Note: The post install scripts will ask you to fill in some settings, including database settings.*

       ```bash
       $ cd /<directory>/
       $ curl -sS https://getcomposer.org/installer | php
       $ php -d memory_limit=-1 composer.phar run-script post-install-cmd
       ```

    B. **Install via Composer**

     You can get eZ Platform using Composer with the following commands:

     *Note: composer will take its time to download all libraries and when done you will be asked to fill in some settings, including database settings.*

       ```bash
       $ curl -sS https://getcomposer.org/installer | php
       $ php -d memory_limit=-1 composer.phar create-project ezsystems/ezplatform ezplatform ^2.0
       $ cd /ezplatform/
       ```

     Arguments *(also see `composer create-project -h`)*:
       - `<package>`: Distribution to install, `ezsystems/ezplatform` is a clean installs of eZ Platform, others:
        - `ezsystems/ezplatform-demo`: Adds a demo site as an example of eZ Platform web site.
        - `ezsystems/ezplatform-ee`: Commercial flavour that adds additional capabilities, see [ez.no](https://ez.no/Products/eZ-Platform-Enterprise-Edition).
       - `<directory>`: Folder to extract to, if omitted same as package name. In example specified to `ezplatform`.
       - `<version>`: Optional, *when omitted you'll get latest stable*. Examples:
        - `^2.0@beta`: To pick latests 2.x beta release, to pick latests 2.0.x release use `~2.0.0`.
        - `v2.0.1` : To pick a specific tag, could also have been `v2.0.0-beta4`
        - `dev-master` : Picks a development version from a branch, here `master`.

     Further reading: https://getcomposer.org/doc/03-cli.md#create-project

2. **Setup folder rights**<a name="install-2-folder-rights"></a>:

       Like most things, [Symfony documentation](http://symfony.com/doc/3.4/setup/file_permissions.html)
       applies here, in this case meaning `var`, `web/var` need to be writable by cli and web server user.
       Furthermore, future files and directories created by these two users will need to inherit those access rights. *For
       security reasons, in production there is no need for web server to have access to write to other directories.*

       For development setup you may change your web server config to use same user as owner of folder, what follows
       below are mainly for production setup, and like Symfony we first and foremost recommend using an approach using ACL.

       A. **Using ACL on a *Linux/BSD* system that supports chmod +a**

       Some systems, primarily Mac OS X, supports setting ACL using a `+a` flag on `chmod`. Example uses a command to
       try to determine your web server user and set it as ``HTTPDUSER``, alternatively change to your actual web server
       user if non standard:

       ```bash
       $ rm -rf var/cache/* var/logs/*
       $ HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)
       $ sudo chmod +a "$HTTPDUSER allow delete,write,append,file_inherit,directory_inherit" var web/var
       $ sudo chmod +a "$(whoami) allow delete,write,append,file_inherit,directory_inherit" var web/var
       ```

       B. **Using ACL on a *Linux/BSD* system that does not support chmod +a**

       Some systems don't support chmod +a, but do support another utility called setfacl. You may need to
       [enable ACL support](https://help.ubuntu.com/community/FilePermissionsACLs) on your partition and install setfacl
       before using it *(as is the case with Ubuntu)*. With it installed example below uses a command to try to determine
       your web server user and set it as ``HTTPDUSER``, alternatively change to your actual web user if non standard:

       ```bash
       $ HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)
       # if this does not work, try adding '-n' option
       $ sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX var web/var
       $ sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX var web/var
       ```

       C. **Using chown on *Linux/BSD/OS X* systems that don't support ACL**

       Some systems don't support ACL at all. You will need to set your web server's user as the owner of the required
       directories, in this setup further symfony console commands against installation should use the web server user
       as well to avoid new files being created using another user.  Example uses a command to try to determine your
       web server user and set it as ``HTTPDUSER``, alternatively change to your actual web server user if non standard:

       ```bash
       $ HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)
       $ sudo chown -R "$HTTPDUSER":"$HTTPDUSER" var web/var
       $ sudo find {web/{var},var} -type d | xargs sudo chmod -R 775
       $ sudo find {web/{var},var} -type f | xargs sudo chmod -R 664
       ```

       D. **Using chmod on a *Linux/BSD/OS X* system where you can't change owner**

       If you can't use ACL and aren't allowed to change owner, you can use chmod, making the files writable by everybody.
       Note that this method really isn't recommended as it allows any user to do anything.

       ```bash
       $ sudo find {web/{var},var} -type d | xargs sudo chmod -R 777
       $ sudo find {web/{var},var} -type f | xargs sudo chmod -R 666
       ```

       When using chmod, note that newly created files (such as cache) owned by the web server's user may have different/restrictive permissions.
       In this case, it may be required to change the umask so that the cache and log directories will be group-writable or world-writable (`umask(0002)` or `umask(0000)` respectively).

       It may also possible to add the group ownership inheritance flag so new files inherit the current group, and use `775`/`664` in the command lines above instead of world-writable:
       ```bash
       $ sudo chmod g+s {web/{var},var}
       ```

       E. **Setup folder rights on Windows**

       For your choice of web server you'll need to make sure web server user has read access to `<root-dir>`, and
       write access to the following directories:
       - web/var
       - var


3. **Run installation command**<a name="install-4-db-setup"></a>:

    You may now complete the eZ Platform installation with ezplatform:install command, example of use:

    ```bash
    $ php -d memory_limit=-1 bin/console ezplatform:install --env prod clean
    ```

    **Note**: Password for the generated `admin` user is `publish`, this name and password is needed when you would like to login to backend Admin UI where you can and should change this.


4. **Configure a VirtualHost**<a name="install-3-vhost"></a>:

    #### Recommended use
    Configure virtual host by either taking examples from [Nginx](doc/nginx) or [Apache2](doc/apache2) documentation,
    or by using provided script to generate from templates, for help see `./bin/vhost.sh -h`, example:
    ```bash
    ./bin/vhost.sh --basedir=/var/www/ezplatform \
      --template-file=doc/apache2/vhost.template \
      --host-name=ezplatform \
      | sudo tee /etc/apache2/sites-enabled/ezplatform.conf > /dev/null
    ```
    Check and adapt the generated vhost config, and then restart Apache or Nginx.

    #### Testing use
    For just local testing without installing a full web-server, while slow you can also run PHP's built-in
    web server using the following command:
    ```bash
    $ php bin/console server:run
    ```


You can now point your browser to the installation and browse the site. To access the Admin UI backend, use the `/admin` URL.
