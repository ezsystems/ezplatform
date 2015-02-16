# Installation instructions

  These are instructions for installing via GIT (development version), look in INSTALL_ARCHIVE.md for instructions on how to install a eZ Platform build/archive.

## Paths for future reference
  * `/<root-dir>/`: The filesystem path where eZ Platform is installed in

    Examples: `/home/myuser/www/` or `/var/sites/<project-name>/`

  * `/<root-dir>/web/`: A directory meant to be the **DocumentRoot** of the eZ Platform installation (`<root-dir>` is not supposed to be _readable_ from web server perspective)

    Examples: `/home/myuser/www/web/` or `/var/sites/<project-name>/web/`

## Clean install

1. You can get eZ Platform using composer with the following commands:

       ```bash
       curl -s http://getcomposer.org/installer | php
       php -d memory_limit=-1 composer.phar create-project --no-dev --prefer-dist ezsystems/ezpublish-community <directory> <version>
       cd /<directory>/
       ```
       
      Options:
      - `<version>`: `dev-master` to get current development version (pre release), `v2015.01` to pick a specific release, otherwise skip it to get latest stable release.
      - For core development change '--prefer-dist' to '--prefer-source' to get full git clones, and remove '--no-dev' to get things like phpunit and behat installed.

  After running command above, composer will take its time to download all libraries, then a wizard will ask you for setting to database and lastly a welcome screen should grant you instructions for finishing installation.


2. Setup folder rights **For *NIX users**:

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

# Updating

  Every time you want to get the latest updates of all your dependencies just run this command:

  ```bash
  $ cd /<root-dir>/
  $ php -d memory_limit=-1 composer.phar update --prefer-dist
  ```

# Upgrading

  Upgrade instructions are not written yet, they will before final version of eZ Platform. In the meantime follow upgrade instructions for eZ Publish 5.x as a guide if you already want to test your Platform code from 5.x on eZ Platform:
  http://doc.ez.no/eZ-Platform/Upgrading/Upgrading-to-5.0/Upgrading-from-4.7-to-5.0


## Configure the system

1. *Optional* Dump your assets in your webroot:

      This step is optional as it is automatically done for you when you install / update vendors via composer which
      you did a few steps up. However during development you will need to execute these (especially last one) to get
      assets to be updated in prod environment, so they are kept here for reference.

       ```bash
       php ezpublish/console assets:install --symlink web
       php ezpublish/console assetic:dump --env=prod web
       ```
       The first command will symlink all the assets from your bundles in the `web/` folder, in a `bundles/` sub-folder.
       The second command will generate the CSS and JavaScript files for the *prod* environement.

       In those commands, "web" is the default folder. In the first two commands, --relative can be added for relative symlinks and further help is available with -h.

       **Note:(1)** you should **not** run the *ezpublish/console* command as root, as it will generate cache files which the webserver and command line users will not be able to update or delete later. If sudo is installed, then you can run it with `-u www-data` for instance (means that you are root or another user who has sudo rights as `www-data`). Example:
       ```bash
       $ sudo -u www-data php ezpublish/console assets:install --symlink web
       ```
       **Note:(2)** if you are deploying ez platform on windows 7+, you need to run the command as Administrator to avoid the following error:

       > [Symfony\Component\Filesystem\Exception\IOException]
       > Unable to create symlink due to error code 1314: 'A required privilege is not held by the client'. Do you have the required Administrator-rights?
       > This is the first solution. But you can also edit the composer.json file by changing "symfony-assets-install": "symlink" to "symfony-assets-install": ""

2. *Optional* Configure a VirtualHost:

    See: https://confluence.ez.no/display/EZP/Virtual+host+setup


