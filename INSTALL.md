# Installation instructions

  These are instructions for installing via GIT (development version), look in INSTALL_ARCHIVE.md for instructions on how to install a eZ Publish 5 build/archive.

## Paths for future reference
  * `/<ezpublish5-root-dir>/`: The filesystem path where eZ Publish 5 is installed in,
    * Examples: "/home/myuser/www/" or "/var/sites/ezpublish/"
  * `/<ezpublish5-root-dir>/ezpublish_legacy/`
    * **Legacy** aka **Legacy Stack** refers to the eZ Publish 4.x installation which is bundled with eZ Publish 5 inside `ezpublish_legacy/`
    * **Examples**: `/home/myuser/www/ezpublish_legacy/` or `/var/sites/ezpublish/ezpublish_legacy/`

## Installation

1. You can get eZ Publish using GIT with the following command:

       ```bash
       git clone https://github.com/ezsystems/ezpublish5.git
       ```

2. Get eZ Publish Legacy

       ```bash
       cd /<ezpublish5-root-dir>/
       git clone https://github.com/ezsystems/ezpublish.git ezpublish_legacy
       ```

       **Important note:** By doing so, you'll need to have [Zeta Components installed and available](http://zetacomponents.org/documentation/install.html) from your include path.

3. *Optional* Upgrade eZ Publish Community Project installation
    1. Start from / upgrade to [latest](http://share.ez.no/downloads/downloads) eZ Publish CP installation.

    2. Follow normal eZ Publish upgrade procedures for upgrading the distribution files and moving over extensions as found here:
       http://doc.ez.no/eZ-Publish/Upgrading/Upgrading-to-5.0/Upgrading-from-4.7-to-5.0

4. Install the dependencies with [Composer](http://getcomposer.org).

       Download composer and install dependencies by running:
       ```bash
       cd /<ezpublish5-root-dir>/
       curl -s http://getcomposer.org/installer | php
       php composer.phar install
       ```

       Note: Every time you want to get the latest updates of all your dependencies just run this command:
       ```bash
       cd /<ezpublish5-root-dir>/
       php composer.phar update
       ```

5. Setup folder rights **For *NIX users**:

       One common issue is that the `ezpublish/cache`, `ezpublish/logs` and `ezpublish/config` directories **must be writable both by the web server and the command line user**.
       If your web server user is different from your command line user, you can run the following commands just once in your project to ensure that permissions will be set up properly.

       Change `www-data` to your web server user:

       A. **Using ACL on a system that supports chmod +a**

       ```bash
       $ rm -rf ezpublish/cache/*
       $ rm -rf ezpublish/logs/*
       $ sudo chmod +a "www-data allow delete,write,append,file_inherit,directory_inherit" \
         ezpublish/{cache,logs,config} ezpublish_legacy/{design,extension,settings,var}
       $ sudo chmod +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" \
         ezpublish/{cache,logs,config} ezpublish_legacy/{design,extension,settings,var}
       ```

       B. **Using ACL on a system that does not support chmod +a**

       Some systems don't support chmod +a, but do support another utility called setfacl. You may need to enable ACL support on your partition and install setfacl before using it (as is the case with Ubuntu), like so:

       ```bash
       $ sudo setfacl -R -m u:www-data:rwx -m u:www-data:rwx \
         ezpublish/{cache,logs,config} ezpublish_legacy/{design,extension,settings,var}
       $ sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx \
         ezpublish/{cache,logs,config} ezpublish_legacy/{design,extension,settings,var}
       ```

       C. **Using chown on systems that don't support ACL**

       Some systems don't support ACL at all. You will either need to set your web server's user as the owner of the required directories.

       ```bash
       $ sudo chown -R www-data:www-data ezpublish/{cache,logs,config} ezpublish_legacy/{design,extension,settings,var}
       $ sudo find {ezpublish/{cache,logs,config},ezpublish_legacy/{design,extension,settings,var}} -type d | xargs chmod -R 775
       $ sudo find {ezpublish/{cache,logs,config},ezpublish_legacy/{design,extension,settings,var}} -type f | xargs chmod -R 664
       ```

       D. **Using chmod**

       If you can't use ACL and aren't allowed to change owner, you can use chmod, making the files writable by everybody. Note that this method really isn't recommended as it allows any user to do anything.

       ```bash
       $ sudo find {ezpublish/{cache,logs,config},ezpublish_legacy/{design,extension,settings,var}} -type d | xargs chmod -R 777
       $ sudo find {ezpublish/{cache,logs,config},ezpublish_legacy/{design,extension,settings,var}} -type f | xargs chmod -R 666
       ```

## Setup files
1. *Optional* Upgrade Configuration: Generate eZ Publish 5 yml configuration

       **Note: this step in only valid for upgrades and can be ignored if you intend to run the setup wizard.**

       To generate yml configuration for the new Symfony stack a console command has been provided to
       cover single site setups.

       Perform the following command where `<group>` is the siteaccess group name, for instance package name like
       'ezdemo_site', 'ezwebin_site' or 'ezflow_site'. And `<admin_siteaccess>` is, for instance, 'ezdemo_site_admin':

       ```bash
       cd /<ezpublish5-root-dir>/
       php ezpublish/console ezpublish:configure --env=prod <group> <admin_siteaccess>
       ```

       If you instead would like to manually create your yml config, do the following:
       * Copy `ezpublish/config/ezpublish.yml.example` to `ezpublish/config/ezpublish_prod.yml`
       * Edit `ezpublish/config/ezpublish_prod.yml`


2. Dump your assets in your webroot:

       ```bash
       php ezpublish/console assets:install --symlink web
       php ezpublish/console ezpublish:legacy:assets_install --symlink web
       ```
       The first command will symlink all the assets from your bundles in the `web/` folder, in a `bundles/` sub-folder.

       The second command will symlink assets from your eZ Publish legacy directory and add wrapper scripts around the legacy front controllers
       (basically `index_treemenu.php`, `index_rest.php` and `index_cluster.php`)

       In both cases "web" is the default folder, --relative can be added for relative symlinks and further help is available with -h.

3. *Optional* - Configure a VirtualHost:

    See: http://doc.ez.no/eZ-Publish/Technical-manual/5.x/Installation/Virtual-host-setup


### Clean installation using Setup wizard
1. Run Setup wizard:

  Access http://`<your-host-name>`/ezsetup to trigger the setup wizard.

##### Troubleshooting during Setup wizard

  You might get the following error:
  > Retrieving remote site packages list failed. You may upload packages manually.
  >
  > Remote repository URL: http://packages.ez.no/ezpublish/5.0/5.0.0[-alpha1]/

  This should only happen when you install from GIT or use pre-release packages
  To fix it, tweak your `settings/package.ini` by overriding it with a valid version:

  ```ini
  [RepositorySettings]
  RemotePackagesIndexURL=http://packages.ez.no/ezpublish/5.0/5.0.0-alpha1
  ```

