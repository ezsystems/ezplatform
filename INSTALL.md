# Installation instructions

  These are instructions for installing via GIT (development version), look in INSTALL_ARCHIVE.md for instructions on how to install a eZ Publish 5 build/archive.

## Paths for future reference
  * `/<ezpublish-community-root-dir>/`: The filesystem path where eZ Publish 5 is installed in

    Examples: `/home/myuser/www/` or `/var/sites/ezpublish/`
  * `/<ezpublish-community-root-dir>/ezpublish_legacy/`: **Legacy** aka **Legacy Stack** refers to the eZ Publish 4.x installation which is bundled with eZ Publish 5 inside `ezpublish_legacy/`

    Examples: `/home/myuser/www/ezpublish_legacy/` or `/var/sites/ezpublish/ezpublish_legacy/`
  * `/<ezpublish-community-root-dir>/web/`: A directory meant to be the **DocumentRoot** of the eZ Publish 5.x installation (`ezpublish` & `ezpublish_legacy` are not supposed to be _viewable_ from user perspective)

    Examples: `/home/myuser/www/web/` or `/var/sites/ezpublish/web/`

## Install all components

1. You can get eZ Publish using GIT with the following command:

       ```bash
       git clone https://github.com/ezsystems/ezpublish-community.git
       ```

2. *Optional* Upgrade an eZ Publish installation

       Follow normal eZ Publish upgrade procedures for upgrading the distribution files and moving over extensions as found for instance here:
       http://doc.ez.no/eZ-Publish/Upgrading/Upgrading-to-5.0/Upgrading-from-4.7-to-5.0

3. Install the dependencies with [Composer](http://getcomposer.org).

       **Note: The following step will also install assets, if you prefer to install assets using hard copy or symlink
               instead of default relative symlink, edit 'symfony-assets-install' setting in composer.json**

       **Dev: For dev use change '--prefer-dist' for '--prefer-source' to get full git clones
              and add '--dev' to get phpunit and behat installed.**

       Download composer and install dependencies by running:
       ```bash
       cd /<ezpublish-community-root-dir>/
       curl -s http://getcomposer.org/installer | php
       php -d memory_limit=-1 composer.phar install --prefer-dist
       ```

       Update note: Every time you want to get the latest updates of all your dependencies just run this command:
       ```bash
       cd /<ezpublish-community-root-dir>/
       php -d memory_limit=-1 composer.phar update --prefer-dist
       ```

4. Setup folder rights **For *NIX users**:

       One common issue is that the `ezpublish/cache`, `ezpublish/logs` and `ezpublish/config` directories **must be writable both by the web server and the command line user**.
       If your web server user is different from your command line user, you can run the following commands just once in your project to ensure that permissions will be set up properly.

       Change `www-data` to your web server user:

       A. **Using ACL on a system that supports chmod +a**

       ```bash
       $ rm -rf ezpublish/cache/*
       $ rm -rf ezpublish/logs/*
       $ rm -rf ezpublish/sessions/*
       $ sudo chmod +a "www-data allow delete,write,append,file_inherit,directory_inherit" \
         ezpublish/{cache,logs,config,sessions} ezpublish_legacy/{design,extension,settings,var} web
       $ sudo chmod +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" \
         ezpublish/{cache,logs,config,sessions} ezpublish_legacy/{design,extension,settings,var} web
       ```

       B. **Using ACL on a system that does not support chmod +a**

       Some systems don't support chmod +a, but do support another utility called setfacl. You may need to enable ACL support on your partition and install setfacl before using it (as is the case with Ubuntu), like so:

       ```bash
       $ sudo setfacl -R -m u:www-data:rwx -m u:www-data:rwx \
         ezpublish/{cache,logs,config,sessions} ezpublish_legacy/{design,extension,settings,var} web
       $ sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx \
         ezpublish/{cache,logs,config,sessions} ezpublish_legacy/{design,extension,settings,var} web
       ```

       C. **Using chown on systems that don't support ACL**

       Some systems don't support ACL at all. You will either need to set your web server's user as the owner of the required directories.

       ```bash
       $ sudo chown -R www-data:www-data ezpublish/{cache,logs,config,sessions} ezpublish_legacy/{design,extension,settings,var} web
       $ sudo find {ezpublish/{cache,logs,config,sessions},ezpublish_legacy/{design,extension,settings,var},web} -type d | sudo xargs chmod -R 775
       $ sudo find {ezpublish/{cache,logs,config},ezpublish_legacy/{design,extension,settings,var},web} -type f | sudo xargs chmod -R 664
       ```

       D. **Using chmod**

       If you can't use ACL and aren't allowed to change owner, you can use chmod, making the files writable by everybody. Note that this method really isn't recommended as it allows any user to do anything.

       ```bash
       $ sudo find {ezpublish/{cache,logs,config,sessions},ezpublish_legacy/{design,extension,settings,var},web} -type d | sudo xargs chmod -R 777
       $ sudo find {ezpublish/{cache,logs,config,sessions},ezpublish_legacy/{design,extension,settings,var},web} -type f | sudo xargs chmod -R 666
       ```

## Configure the system

1. *Optional* Upgrade Configuration: Generate eZ Publish 5 yml configuration

       **Note: this step in only valid for upgrades and can be ignored if you intend to run the setup wizard.**

       To generate yml configuration for the new Symfony stack a console command has been provided to
       cover single site setups.

       Perform the following command where `<group>` is the siteaccess group name, for instance package name like
       'ezdemo_site', 'ezwebin_site' or 'ezflow_site'. And `<admin_siteaccess>` is, for instance, 'ezdemo_site_admin':

       ```bash
       cd /<ezpublish-community-root-dir>/
       php ezpublish/console ezpublish:configure --env=prod <group> <admin_siteaccess>
       ```

       If you instead would like to manually create your yml config, do the following:
       * Copy `ezpublish/config/ezpublish.yml.example` to `ezpublish/config/ezpublish_prod.yml`
       * Edit `ezpublish/config/ezpublish_prod.yml`

2. *Optional* Dump your assets in your webroot:

      This step is optional as it is automatically done for you when you install / update vendors via composer which
      you did a few steps up. However during development you will need to execute these (especially last one) to get
      assets to be updated in prod environment, so they are kept here for reference.

       ```bash
       php ezpublish/console assets:install --symlink web
       php ezpublish/console ezpublish:legacy:assets_install --symlink web
       php ezpublish/console assetic:dump --env=prod web
       ```
       The first command will symlink all the assets from your bundles in the `web/` folder, in a `bundles/` sub-folder.

       The second command will symlink assets from your eZ Publish legacy directory and add wrapper scripts around the legacy front controllers
       (basically `index_treemenu.php`, `index_rest.php` and `index_cluster.php`)

       The third command will generate the CSS and JavaScript files for the *prod* environement.

       In those commands, "web" is the default folder. In the first two commands, --relative can be added for relative symlinks and further help is available with -h.

       **Note:(1)** you should **not** run the *ezpublish/console* command as root, as it will generate cache files which the webserver and command line users will not be able to update or delete later. If sudo is installed, then you can run it with `-u www-data` for instance (means that you are root or another user who has sudo rights as `www-data`). Example:
       ```bash
       $ sudo -u www-data php ezpublish/console assets:install --symlink web
       ```
       **Note:(2)** if you are deploying ez publish 5 on windows 7+, you need to run the command as Administrator to avoid the following error:

       > [Symfony\Component\Filesystem\Exception\IOException]
       > Unable to create symlink due to error code 1314: 'A required privilege is not held by the client'. Do you have the required Administrator-rights?
       > This is the first solution. But you can also edit the composer.json file by changing "symfony-assets-install": "symlink" to "symfony-assets-install": ""

3. *Optional* Configure a VirtualHost:

    See: https://confluence.ez.no/display/EZP/Virtual+host+setup

4. Run the Setup wizard:

  **Note: this step in only valid for clean install and can be ignored if you are performing an upgrade.**

  In Virtual host mode access http://`<your-host-name>`/ezsetup to trigger the setup wizard.
  In Non-Virtual Host mode access eZ Publish like: http://localhost/ezp5/index.php/ezsetup

  **Troubleshooting during Setup wizard**

  You might get the following error:
  > Retrieving remote site packages list failed. You may upload packages manually.
  >
  > Remote repository URL: http://packages.ez.no/ezpublish/5.0/5.0.0[-alpha1]/

  This should only happen when you install from GIT or use pre-release packages
  To fix it, tweak your `ezpublish_legacy/settings/package.ini` by overriding it with latest valid version, for example:

  ```ini
  [RepositorySettings]
  RemotePackagesIndexURL=http://packages.ez.no/ezpublish/5.0/5.0.0
  ```

