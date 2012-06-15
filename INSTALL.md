# Installation instructions

1. Clone the repository

       ```bash
       git clone git@github.com:ezsystems/ezp-next-mvc.git
       ```
2. Install the dependencies with [Composer](http://getcomposer.org):

       ```bash
       cd /path/to/ezp-next-mvc/
       php composer.phar install
       ```
3. Initialize and update git submodules (like public API):

       ```bash
       git submodule init
       git submodule update
       ```
4. Configure by editing `app/config/config.yml`:
    * `ez_publish_legacy.root_dir`: Path to your eZ Publish legacy directory. The default path is `app/ezpublish_legacy`
    * `ezpublish.api.storage_engine.legacy.dsn`: DSN to your database connection (only MySQL and PostgreSQL are supported at the moment)

5. Dump your assets in your webroot:

    ```bash
    php app/console assets:install --symlink web
    php app/console ezpublish:legacy:assets_install
    ```
    The first command will symlink all the assets from your bundles in the `web/` folder in a `bundles/` sub-folder.

    The second command will symlink assets from your eZ Publish legacy directory and add wrapper scripts around the legacy front controllers
    (basically `index_treemenu.php`, `index_rest.php` and `index_cluster.php`)

6. *Optional* - Configure a VirtualHost:

    ```apache
    <VirtualHost *:80>
        ServerName your-host-name
        DocumentRoot /path/to/ezp-next-mvc/web/

        <Directory /path/to/ezp-next-mvc/>
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
6. *Optional*, **Development ONLY** - Take advantage of PHP 5.4 build-in web server:

    ```bash
    php app/console server:run localhost:8000
    ```
    The command above will run the built-in web server on localhost, on port 8000.
    You will have access to eZ Publish by going to `http://localhost:8000` from your browser.
