# Installation instructions

## Terms for future reference:
  * `/<root-dir>/`: The filesystem path where eZ Platform is installed in.
    Examples: `/home/myuser/www/` or `/var/sites/<project-name>/`
  * cli: command line interface. For *Linux/BSD/OS X* specific commands, use of `bash` or similar is assumed.

## Prerequisite

  These instructions assume you have technical knowledge and have already installed PHP, web server &
  *a database server* needed for this software. For further information on requirements [see online doc](https://doc.ez.no/display/TECHDOC/Requirements)

  **Before you start**:
  - Create Database: Installation will ask you for credentials/details for which database to use, example with mysql: 
    `CREATE DATABASE <database> CHARACTER SET utf8;` *Note: Right now installer only supports MySQL and MariaDB, Postgres
    support will be (re)added in one of the upcoming releases.*
  - Set php.ini memory_limit=256M before running commands below
  - *Optional:* You can also setup Solr to be used by eZ Platform and take note of the url it is accessible on

## Install

1. **Install/Extract eZ Platform**<a name="install-1-extract"></a>:

    There are two ways to install eZ Platform described below, what is common is that you should make sure
    relevant settings are generated into `app/config/parameters.yml` as a result of this step.

    `parameters.yml` contains settings for your database, mail system, and optionally [Solr](http://lucene.apache.org/solr/)
    if `search_engine` is configured as `solr`, as opposed to default `legacy` *(a limited database powered search engine)*.

    A. **Extract archive** (tar/zip)

       Download archive from [share.ez.no/downloads](http://share.ez.no/downloads/downloads), __not from GitHub__ *(those are for composer)*.
       Extract the eZ Platform 15.01 *(or higher)* archive to a directory, then execute post install scripts:

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
       $ php -d memory_limit=-1 composer.phar create-project --no-dev ezsystems/ezplatform
       $ cd /ezplatform/
       ```

     Arguments *(also see `php composer.phar create-project -h`)*:
       - `<package>`: Distribution to install, `ezsystems/ezplatform` is a clean installs of eZ Platform, others:
        - `ezsystems/ezplatform-demo`: Adds a demo site as an example of eZ Platform web site.
        - `ezsystems/ezstudio`: Commercial flavour that adds additional capabilities, see [ezstudio.com](http://ezstudio.com/).
       - `<directory>`: Folder to extract to, if omitted same as package name so in example: `ezplatform`.
       - `<version>`: Optional, *when omitted you'll get latest stable*. Examples:
        - `~1.2.0`: To pick latests 1.2 release, to pick latests 1.x release use `~1.2`.
        - `v1.1.0` : To pick a specific tag, could also have been `v1.0.0-rc1`
        - `dev-master` : Picks a development version from a branch, here `master`.

     Further reading: https://getcomposer.org/doc/03-cli.md#create-project

2. **Setup folder rights**<a name="install-2-folder-rights"></a>:

       Like most things, [Symfony documentation](http://symfony.com/doc/current/book/installation.html#checking-symfony-application-configuration-and-setup)
       applies here, in this case meaning `app/cache`, `app/logs` and `web` need to be writable by cli and web server user.
       Furthermore, future files and directories created by these two users will need to inherit those access rights. *For
       security reasons, in production there is no need for web server to have access to write to other directories.*

       For development setup you may change your web server config to use same user as owner of folder, what follows
       below are mainly for production setup, and like Symfony we first and foremost recommend using an approach using ACL.

       A. **Using ACL on a *Linux/BSD* system that supports chmod +a**

       Some systems, primarily Mac OS X, supports setting ACL using a `+a` flag on `chmod`. Example uses a command to
       try to determine your web server user and set it as ``HTTPDUSER``, alternatively change to your actual web server
       user if non standard:

       ```bash
       $ rm -rf app/cache/* app/logs/*
       $ HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
       $ sudo chmod +a "$HTTPDUSER allow delete,write,append,file_inherit,directory_inherit" app/cache app/logs  web
       $ sudo chmod +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" app/cache app/logs web
       ```

       B. **Using ACL on a *Linux/BSD* system that does not support chmod +a**

       Some systems don't support chmod +a, but do support another utility called setfacl. You may need to
       [enable ACL support](https://help.ubuntu.com/community/FilePermissionsACLs) on your partition and install setfacl
       before using it *(as is the case with Ubuntu)*. With it installed example below uses a command to try to determine
       your web server user and set it as ``HTTPDUSER``, alternatively change to your actual web user if non standard:

       ```bash
        $ HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
        $ sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs web
        $ sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs web
       ```

       C. **Using chown on *Linux/BSD/OS X* systems that don't support ACL**

       Some systems don't support ACL at all. You will need to set your web server's user as the owner of the required
       directories, in this setup further symfony console commands against installation should use the web server user
       as well to avoid new files being created using another user.  Example uses a command to try to determine your
       web server user and set it as ``HTTPDUSER``, alternatively change to your actual web server user if non standard:

       ```bash
       $ HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
       $ sudo chown -R "$HTTPDUSER":"$HTTPDUSER" app/cache app/logs web
       $ sudo find {app/{cache,logs},web} -type d | xargs sudo chmod -R 775
       $ sudo find {app/{cache,logs},web} -type f | xargs sudo chmod -R 664
       ```

       D. **Using chmod on a *Linux/BSD/OS X* system where you can't change owner**

       If you can't use ACL and aren't allowed to change owner, you can use chmod, making the files writable by everybody.
       Note that this method really isn't recommended as it allows any user to do anything.

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
       Note: due to a limitation in the Flysystem version required by eZ
       Platform, image variations directories and files are created with a
       hardcoded permission that prevents group users and users other than the
       owner from writing or removing those files/directories.

       E. **Setup folder rights on Windows**

       For your choice of web server you'll need to make sure web server user has read access to `<root-dir>`, and
       write access to the following directories:
       - app/cache
       - app/logs
       - web


3. **Run installation command**<a name="install-4-db-setup"></a>:

    You may now complete the eZ Platform installation with ezplatform:install command, example of use:

    ```bash
    $ php -d memory_limit=-1 app/console ezplatform:install --env prod clean
    ```

    **Note**: Password for the generated `admin` user is `publish`, this name and password is needed when you would like to login to backend Platform UI. Future versions will prompt you for a unique password during installation.


4. **Configure a VirtualHost**<a name="install-3-vhost"></a>:

    #### Recommended use
    Configure virtual host by either taking examples from [Nginx](doc/nginx) or [Apache2](doc/apache2) documentation,
    or by using provided script to generate from templates, for help see `./bin/vhost.sh -h`, example:
    ```bash
    ./bin/vhost.sh --basedir=/var/www/ezplatform \\
      --template-file=doc/apache2/vhost.template \\
      --host-name=ezplatform \\
      | sudo tee /etc/apache2/sites-enabled/ezplatform.conf > /dev/null
    ```
    Check and adapt the generated vhost config, and then restart Apache or Nginx.

    #### Testing use
    For just local testing without installing a full web-server, while slow you can also run PHP's built-in
    web server using the following command:
    ```bash
    $ php app/console server:run
    ```

    *Note: While far from meant for production use, you can run the command above with `--env=prod` to disable debug.*


You can now point your browser to the installation and browse the site. To access the Platform UI backend, use the `/ez` URL.
