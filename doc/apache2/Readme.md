Apache 2.2 / 2.4  configuration
===============================

For recommended versions of [Apache](https://httpd.apache.org/), see [online requirements](https://doc.ez.no/display/TECHDOC/Requirements).


Prerequisites
-------------
- Some general knowledge of how to install and configure Apache
- Apache 2.x must be installed in prefork mode
- Apache modules: `mod_php`, `mod_rewrite`, `mod_env`, `mod_setenvif`, and optionally `mod_expires`


Configure
---------
These examples are simplified to get you up and running, see "Virtual host template" for more options and details on best practice.

#### Virtual Host

1. Place virtualhost config *(example below)* in a suitable apache config folder, typically:
   - Debian/Ubuntu: `/etc/apache2/sites-enabled/<yoursite>.conf`
   - RHEL/CentOS/Amazon-Linux: `/etc/httpd/conf.d/<yoursite>.conf`
2. Adjust the basics to your setup:
   - [VirtualHost](https://httpd.apache.org/docs/2.4/en/mod/core.html#virtualhost): IP and port number to listen to.
   - [ServerName](https://httpd.apache.org/docs/2.4/en/mod/core.html#servername): Your host name, example `ez.no`.
   - [ServerAlias](https://httpd.apache.org/docs/2.4/en/mod/core.html#serveralias): Optional host alias list, example `www.ez.no login.ez.no`.
   - [DocumentRoot](https://httpd.apache.org/docs/2.4/en/mod/core.html#documentroot): Point this and *Directory* to "web" directory of eZ installation.
3. Restart Apache, normally:
   - Debian/Ubuntu: `sudo service apache2 restart`
   - RHEL/CentOS/Amazon-Linux: `sudo service httpd restart`

Example config for Apache 2.4 in prefork mode:

    <VirtualHost *:80>
        ServerName localhost
        #ServerAlias *.localhost
        DocumentRoot /var/www/ezplatform/web
        DirectoryIndex app.php

        <Directory /var/www/ezplatform/web>
            Options FollowSymLinks
            AllowOverride None
            # Depending on your global Apache settings, you may uncomment this:
            Require all granted
        </Directory>

        # As we require ´mod_rewrite´  this is on purpose not placed in a <IfModule mod_rewrite.c> block
        RewriteEngine On

        # Environment.
        # Possible values: "prod" and "dev" out-of-the-box, other values possible with proper configuration
        # Defaults to "prod" if omitted. If Apache complains about this line and you can't install `mod_setenvif` then
        # comment out "%{ENV:SYMFONY_ENV}" line below, and comment this out or set via: SetEnv SYMFONY_ENV "prod"
        SetEnvIf Request_URI ".*" SYMFONY_ENV=prod

        # Sets the HTTP_AUTHORIZATION header sometimes removed by Apache
        RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

        # Access to repository images in single server setup 
        RewriteRule ^/var/([^/]+/)?storage/images(-versioned)?/.* - [L]

        # Makes it possible to place your favicon at the root of your eZ Platform instance.
        # It will then be served directly.
        RewriteRule ^/favicon\.ico - [L]
        RewriteRule ^/robots\.txt - [L]

        # The following rule is needed to correctly display assets from eZ Platform / Symfony bundles
        RewriteRule ^/bundles/ - [L]

        # Additional Assetic rules for environments different from dev,
        # remember to run php app/console assetic:dump --env=prod
        RewriteCond %{ENV:SYMFONY_ENV} !^(dev)
        RewriteRule ^/(css|js|font)/.*\.(css|js|otf|eot|ttf|svg|woff) - [L]

        RewriteRule .* /app.php
    </VirtualHost>


#### .httaccess

If you don't have access to use virtualhost config this simplified `.htaccess` file can be placed in `web/` folder to
get up and running. *This will not work if Apache is configured with `AllowOverride None` for this directory.*

    DirectoryIndex app.php

    # Disabling MultiViews prevents unwanted negotiation, e.g. "/app" should not resolve
    # to the front controller "/app.php" but be rewritten to "/app.php/app".
    <IfModule mod_negotiation.c>
        Options -MultiViews
    </IfModule>

    # As we require ´mod_rewrite´  this is on purpose not placed in a <IfModule mod_rewrite.c> block
    RewriteEngine On

    # Environment.
    # Possible values: "prod" and "dev" out-of-the-box, other values possible with proper configuration
    # Defaults to "prod" if omitted.
    SetEnv SYMFONY_ENV "prod"

    # Sets the HTTP_AUTHORIZATION header sometimes removed by Apache
    RewriteCond %{HTTP:Authorization} .
    RewriteRule ^ - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Makes it possible to placed your favicon and robots.txt at the root of your web folder
    RewriteRule ^favicon\.ico - [L]
    RewriteRule ^robots\.txt - [L]

    # To display assets from eZ Platform / Symfony bundles
    RewriteRule ^bundles/ - [L]

    # Access to repository images in single server setup
    RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* - [L]

    # Additional Assetic rules for prod environments
    # ! Remember to run php ezpublish/console assetic:dump --env=prod on changes
    # ! Or if SYMFONY_ENV is set to "dev", comment this out!
    RewriteRule ^(css|js|font)/.*\.(css|js|otf|eot|ttf|svg|woff) - [L]

    # Rewrite all other queries to the front controller.
    RewriteRule .* app.php


Virtual host template
---------------------
This folder contains `vhost.template` containing more features you can enable in your virtual host configuration. You may
also use this for .httaccess config, however you will need to adjust rewrite rules to remove `/` like example above.

Bash script *(Unix/Linux/OS X)* exists to be able to generate the configuration.For help text, execute following from
eZ install root:
```bash
./bin/vhost.sh -h
```

#### Common issues

##### NameVirtualHost conflicts

The "NameVirtualHost" setting might already exist in the default configuration. Defining a new one will result in a
conflict. If Apache reports errors such as "NameVirtualHost [IP_ADDRESS] has no VirtualHosts" or "Mixing * ports and
non-* ports with a NameVirtualHost address is not supported", try removing the NameVirtualHost line. See
[more info about the NameVirtualHost directive](http://httpd.apache.org/docs/2.4/mod/core.html#namevirtualhost)
