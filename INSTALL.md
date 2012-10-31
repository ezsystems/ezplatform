# Installation instructions

## Paths for future reference
* `/<ezpubish5-root-dir>/`: The filesystem path where eZ Publish 5 is installed in, examples: "/home/myuser/www/" or "/var/sites/ezpublish/"
* `/<ezpubish5-root-dir>/ezpublish_legacy/`:
	* "Legacy" aka "Legacy Stack" refers to the eZ Publish 4.x installation which is bundled with eZ Publish 5 normally inside "ezpublish_legacy/"
	* Example: "/home/myuser/www/ezpublish_legacy/"

## Installation

### A: From Archive (tar.gz)
1. Extract the archive

   **For upgrading from eZ Publish Enterprise Edition 4.7**: Upgrade documentation can be found on http://doc.ez.no/eZ-Publish/Upgrading/Upgrading-to-5.0/Upgrading-from-4.7-to-5.0

### B: From GIT **Development ONLY**
1. You can get eZ Publish using GIT with the following command:
       ```bash
       git clone https://github.com/ezsystems/ezpublish5.git
       ```

2. Get eZ Publish Legacy

       ```bash
       cd /<ezpubish5-root-dir>/
       git clone https://github.com/ezsystems/ezpublish.git ezpublish_legacy
       ```

       **Important note:** By doing so, you'll need to have [Zeta Components installed and available](http://zetacomponents.org/documentation/install.html) from your include path.

3. *Optional* Upgrade eZ Publish Community Project installation
    1. Start from / upgrade to [latest](http://share.ez.no/downloads/downloads) eZ Publish CP installation.

    2. Follow normal eZ Publish upgrade procedures for upgrading the distribution files and moving over extensions.

4. Install the dependencies with [Composer](http://getcomposer.org).

       Download composer and install dependencies by running:
       ```bash
       cd /<ezpubish5-root-dir>/
       curl -s http://getcomposer.org/installer | php
       php composer.phar install
       ```

       Note: Every time you want to get the latest updates of all your dependencies just run this command:
       ```bash
       cd /<ezpubish5-root-dir>/
       php composer.phar update
       ```

## Setup files
1. Configure:

    **Note: this step can be ignored if you run the setup wizard as explained in a later section.**

    * Copy `ezpublish/config/ezpublish.yml.example` to `ezpublish/config/ezpublish.yml`
    * Edit `ezpublish/config/ezpublish.yml` and configure

         * `ezpublish.system.ezdemo_group.database`: Your database settings (only MySQL and PostgreSQL are supported at the moment)
         * `ezpublish.siteaccess.default_siteaccess`: Should be a **valid siteaccess** (preferably the same than `[SiteSettings].DefaultAccess` set in your `settings/override/site.ini.append.php`

2. Dump your assets in your webroot:

    ```bash
    php ezpublish/console assets:install --symlink web
    php ezpublish/console ezpublish:legacy:assets_install --symlink web
    ```
    The first command will symlink all the assets from your bundles in the `web/` folder, in a `bundles/` sub-folder.

    The second command will symlink assets from your eZ Publish legacy directory and add wrapper scripts around the legacy front controllers
    (basically `index_treemenu.php`, `index_rest.php` and `index_cluster.php`)

    In both cases "web" is the default folder, --relative can be added for relative symlinks and further help is available with -h.

> **Side note for Linux users**:
> One common issue is that the ezpublish/cache and ezpublish/logs directories must be writable both by the web server and the command line user.
> If your web server user is different from your command line user, you can run the following commands just once in your project to ensure that permissions will be set up properly. Change www-data to your web server user:
> **1. Using ACL on a system that supports chmod +a**
```
 $ rm -rf ezpublish/cache/*
 $ rm -rf ezpublish/logs/*
 $ sudo chmod +a "www-data allow delete,write,append,file_inherit,directory_inherit" ezpublish/cache ezpublish/logs
 $ sudo chmod +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" ezpublish/cache ezpublish/logs
```
> **2. Using ACL on a system that does not support chmod +a**
> Some systems don't support chmod +a, but do support another utility called setfacl. You may need to enable ACL support on your partition and install setfacl before using it (as is the case with Ubuntu), like so:
```
$ sudo setfacl -R -m u:www-data:rwx -m u:www-data:rwx ezpublish/cache ezpublish/logs
$ sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx ezpublish/cache ezpublish/logs
```

3. *Optional* - Configure a VirtualHost:

    ```apache
    <VirtualHost *:80>
        ServerName your-host-name
        DocumentRoot /<ezpubish5-root-dir>/web/

        <Directory /<ezpubish5-root-dir>/>
            Options FollowSymLinks
            order allow,deny
            allow from all
        </Directory>

        RewriteEngine On
        # Uncomment in FastCGI mode, to get basic auth working.
        #RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
        # v1 rest API is on Legacy
        RewriteRule ^/api/[^/]+/v1/ /index_rest.php [L]

        # If using cluster, uncomment the following two lines:
        #RewriteRule ^/var/([^/]+/)?storage/images(-versioned)?/.* /index_cluster.php [L]
        #RewriteRule ^/var/([^/]+/)?cache/(texttoimage|public)/.* /index_cluster.php [L]

        RewriteRule ^/var/([^/]+/)?storage/images(-versioned)?/.* - [L]
        RewriteRule ^/var/([^/]+/)?cache/(texttoimage|public)/.* - [L]
        RewriteRule ^/design/[^/]+/(stylesheets|images|javascript|fonts)/.* - [L]
        RewriteRule ^/share/icons/.* - [L]
        RewriteRule ^/extension/[^/]+/design/[^/]+/(stylesheets|flash|images|lib|javascripts?)/.* - [L]
        RewriteRule ^/packages/styles/.+/(stylesheets|images|javascript)/[^/]+/.* - [L]
        RewriteRule ^/packages/styles/.+/thumbnail/.* - [L]
        RewriteRule ^/var/storage/packages/.* - [L]

        #  Makes it possible to place your favicon at the root of your
        #  eZ Publish instance. It will then be served directly.
        RewriteRule ^/favicon\.ico - [L]
        # Uncomment the line below if you want you favicon be served
        # from the standard design. You can customize the path to
        # favicon.ico by changing /design/standard/images/favicon\.ico
        #RewriteRule ^/favicon\.ico /design/standard/images/favicon.ico [L]
        RewriteRule ^/design/standard/images/favicon\.ico - [L]

        # Give direct access to robots.txt for use by crawlers (Google,
        # Bing, Spammers..)
        RewriteRule ^/robots\.txt - [L]

        # Platform for Privacy Preferences Project ( P3P ) related files
        # for Internet Explorer
        # More info here : http://en.wikipedia.org/wiki/P3p
        RewriteRule ^/w3c/p3p\.xml - [L]

        # Uncomment the following lines when using popup style debug in legacy
        #RewriteRule ^/var/([^/]+/)?cache/debug\.html.* - [L]

        # Following rule is needed to correctly display assets from bundles
        RewriteRule ^/bundles/ - [L]

        RewriteRule .* /index.php
    </VirtualHost>
    ```

### Clean installation using Setup wizard
1. Run Setup wizard:

Access http://server/ezsetup to trigger the setup wizard.

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
