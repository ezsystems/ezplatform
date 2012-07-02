# Installation instructions

## eZ Publish 4 (aka *legacy*) part
1. Start from an [eZ Publish CP 2012.5](http://share.ez.no/downloads/downloads/ez-publish-community-project-2012.5) installation

2. Upgrade it to the enhanced version 
   (get the source from eZ Publish legacy's [**ezpublish5-integration** branch](https://github.com/ezsystems/ezpublish/tree/ezpublish5-integration), 
   or just [download the ZIP file](https://github.com/ezsystems/ezpublish/zipball/ezpublish5-integration)). 
   No upgrade script is needed, only replace all source files (except your own extensions, templates and settings).

   > **Very important**: Be sure you have upgraded your **index.php** as well

## eZ Publish 5 part
1. Clone the repository

       ```bash
       git clone git@github.com:ezsystems/ezpublish5.git
       ```
2. Install the dependencies with [Composer](http://getcomposer.org).

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
       
       > **Note**: If you end to a *process timed out* error, this might be caused by a *not-that-fast* internet connection :-).
       > Try then to set `COMPOSER_PROCESS_TIMEOUT` environment variable to 3000 before relaunching the composer install command.
       
       ```bash
       COMPOSER_PROCESS_TIMEOUT=3000 php composer.phar install
       ```
3. Initialize and update git submodules (like public API):

       ```bash
       git submodule update --init
       ```
4. Move (or symlink) your eZ Publish legacy root to `app/ezpublish_legacy`

       ```bash
       ln -s /path/to/ezpublish/legacy /path/to/ezpublish5/app/ezpublish_legacy
       ```

5. Configure by editing `app/config/config.yml`:
    * `ezpublish.api.storage_engine.legacy.dsn`: DSN to your database connection (only MySQL and PostgreSQL are supported at the moment)

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
        
        # If not using cluster:
        RewriteRule ^/var/storage/.* - [L]
        RewriteRule ^/var/[^/]+/storage/.* - [L]
        # If using cluster, uncomment the following and comment the previous ones
        #RewriteRule ^/var/([^/]+/)?storage/images(-versioned)?/.* /index_cluster.php [L]
        #RewriteRule ^/var/([^/]+/)?cache/(texttoimage|public)/.* /index_cluster.php [L]

        RewriteRule ^/var/cache/texttoimage/.* - [L]
        RewriteRule ^/var/[^/]+/cache/(texttoimage|public)/.* - [L]
        RewriteRule ^/design/[^/]+/(stylesheets|images|javascript)/.* - [L]
        RewriteRule ^/share/icons/.* - [L]
        RewriteRule ^/extension/[^/]+/design/[^/]+/(lib|stylesheets|images|javascripts?)/.* - [L]
        RewriteRule ^/packages/styles/.+/(stylesheets|images|javascript)/[^/]+/.* - [L]
        RewriteRule ^/packages/styles/.+/thumbnail/.* - [L]
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
