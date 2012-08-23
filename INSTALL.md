# Installation instructions

> **Side note for Linux users**: Please avoid doing this installation as `root`, since your web server probably runs with another user 
(`www-data` on Debian/Ubuntu, `apache` on Redhat/CentOS/Fedora).
> 
> If you still want to do this as `root`, then ensure that your webserver has at least write access in the `app/` directory.

## eZ Publish 4 (aka *legacy*) part
1. Start from an [eZ Publish CP 2012.5](http://share.ez.no/downloads/downloads/ez-publish-community-project-2012.5) or [higher](http://share.ez.no/downloads/downloads) installation.

2. Upgrade it to the enhanced version 
   (get the source from eZ Publish legacy's [**master** branch](https://github.com/ezsystems/ezpublish/tree/master), 
   or just [download the ZIP file](https://github.com/ezsystems/ezpublish/zipball/master)). 

   > **Very important**: Be sure you have upgraded your **index.php** as well


### Troubleshooting
You might get the following error:
> Retrieving remote site packages list failed. You may upload packages manually.
>
> Remote repository URL: http://packages.ez.no/ezpublish/5.0/5.0.0alpha1/

This is most likely because you didn't start from an eZ Publish CP package, but directly from GitHub sources,
or because you launched installation wizard *after* having upgraded to `ezpublish5-integration` branch.

To fix it, tweak your `settings/package.ini` by overriding it:

```ini
[RepositorySettings]
RemotePackagesIndexURL=http://packages.ez.no/ezpublish/4.7/4.7.0
```

## eZ Publish 5 part
1. Clone the repository

       ```bash
       git clone git@github.com:ezsystems/ezpublish5.git
       ```
       
2. Move (or symlink) your eZ Publish legacy root to `app/ezpublish_legacy`

       ```bash
       ln -s /path/to/ezpublish/legacy /path/to/ezpublish5/app/ezpublish_legacy
       ```

3. Install the dependencies with [Composer](http://getcomposer.org).

       If you don't have Composer yet, download it following the instructions on http://getcomposer.org/ or just run the following command:
       ```bash
       cd /path/to/ezpublish5/
       curl -s http://getcomposer.org/installer | php
       ```

       Afer installing composer, install of all the project's dependencies by running:
       ```bash
       cd /path/to/ezpublish5/
       php composer.phar install
       ```

5. Configure:
    * Copy `app/config/parameters.yml.dist` to `app/config/parameters.yml`
    * Edit `app/config/parameters.yml` and configure

         * `ezpublish.api.storage_engine.legacy.dsn`: DSN to your database connection (only MySQL and PostgreSQL are supported at the moment)
         * `ezpublish.siteaccess.default`: Should be a **valid siteaccess** (preferably the same than `[SiteSettings].DefaultAccess` set in your `settings/override/site.ini.append.php`

6. Dump your assets in your webroot:

    ```bash
    php app/console assets:install --symlink web
    php app/console ezpublish:legacy:assets_install
    ```
    The first command will symlink all the assets from your bundles in the `web/` folder in a `bundles/` sub-folder.

    The second command will symlink assets from your eZ Publish legacy directory and add wrapper scripts around the legacy front controllers
    (basically `index_treemenu.php`, `index_rest.php` and `index_cluster.php`)

7. *Optional* - Configure a VirtualHost:

    ```apache
    <VirtualHost *:80>
        ServerName your-host-name
        DocumentRoot /path/to/ezpublish5/web/

        <Directory /path/to/ezpublish5/>
            Options FollowSymLinks
            order allow,deny
            allow from all
        </Directory>

        RewriteEngine On
        RewriteRule ^/api/ /index_rest.php [L]
        RewriteRule content/treemenu/? /index_treemenu.php [L]
   
        # If using cluster, uncomment the following two lines:
        #RewriteRule ^/var/([^/]+/)?storage/images(-versioned)?/.* /index_cluster.php [L]
        #RewriteRule ^/var/([^/]+/)?cache/(texttoimage|public)/.* /index_cluster.php [L]
        
        RewriteRule ^/var/([^/]+/)?storage/.* - [L]
        RewriteRule ^/var/([^/]+/)?cache/(texttoimage|public)/.* - [L]
        RewriteRule ^/design/([^/]+/)?(stylesheets|images|javascript)/.* - [L]
        RewriteRule ^/share/icons/.* - [L]
        RewriteRule ^/extension/[^/]+/design/[^/]+/(lib|stylesheets|images|javascripts?)/.* - [L]
        RewriteRule ^/packages/styles/.+/(stylesheets|images|javascript)/[^/]+/.* - [L]
        RewriteRule ^/packages/styles/.+/thumbnail/.* - [L]
        RewriteRule ^/var/storage/packages/.* - [L]
        RewriteRule ^/favicon\.ico - [L]
        RewriteRule ^/robots\.txt - [L]
   
        # Following rule is needed to correctly display assets from bundles
        RewriteRule ^/bundles/ - [L]
        RewriteRule .* /index.php
    </VirtualHost>
    ```
7. *Optional*, **Development ONLY** - Take advantage of PHP 5.4 build-in web server:

    ```bash
    php app/console server:run localhost:8000
    ```
    The command above will run the built-in web server on localhost, on port 8000.
    You will have access to eZ Publish by going to `http://localhost:8000` from your browser.
