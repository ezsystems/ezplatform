# Installation instructions

1. Clone the repository

       ```bash
       git clone git@github.com:ezsystems/ezp-next-mvc.git
       ```
2. Install the dependencies with [Composer](http://getcomposer.org) :

       ```bash
       cd /path/to/ezp-next-mvc/
       php bin/composer.phar install
       ```
3. Initialize and update git submodules (like public API) :

       ```bash
       git submodule init
       git submodule update
       ```

4. Configure a VirtualHost with:

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
        RewriteRule ^/var/storage/.* - [L]
        RewriteRule ^/var/[^/]+/storage/.* - [L]
        RewriteRule ^/var/cache/texttoimage/.* - [L]
        RewriteRule ^/var/[^/]+/cache/(texttoimage|public)/.* - [L]
        RewriteRule ^/design/[^/]+/(stylesheets|images|javascript)/.* - [L]
        RewriteRule ^/css/.* - [L]
        RewriteRule ^/share/icons/.* - [L]
        RewriteRule ^/extension/[^/]+/design/[^/]+/(lib|stylesheets|images|javascripts?)/.* - [L]
        RewriteRule ^/packages/styles/.+/(stylesheets|images|javascript)/[^/]+/.* - [L]
        RewriteRule ^/packages/styles/.+/thumbnail/.* - [L]
        RewriteRule ^/favicon\.ico - [L]
        RewriteRule ^/robots\.txt - [L]
        RewriteRule .* /index.php
    </VirtualHost>
    ```