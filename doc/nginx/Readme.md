NGINX configuration
===================

For recommended versions of [NGINX](http://nginx.org/), see [online requirements](https://doc.ez.no/display/TECHDOC/Requirements).


Prerequisites
-------------
- Some general knowledge of how to install and configure NGINX
- A working [PHP FPM](http://php.net/manual/en/install.fpm.php) setup which [NGINX](https://www.nginx.com/resources/wiki/start/topics/examples/phpfcgi/) can be configured to use as fastcgi server *(either using tcp or unix socket)*.
- nginx must be installed.

Configure
---------
This example is simplified to get you up and running, see [Virtual host template](#virtual-host-template) for more options and details on best practice.

#### Virtual Host

1. Place virtualhost config *(example below)* in suitable nginx config folder, typically:
   - Debian/Ubuntu: `/etc/nginx/sites-enabled/<yoursite>.conf`
   - RHEL/CentOS/Amazon-Linux: `/etc/nginx/conf.d/<yoursite>.conf`
2. Adjust the basics to your setup:
   - [listen](http://nginx.org/en/docs/http/ngx_http_core_module.html#listen): IP and port number to listen to.
   - [server_name](http://nginx.org/en/docs/http/ngx_http_core_module.html#server_name): Host list, example `ezplatform.localhost`.
   - [root](http://nginx.org/en/docs/http/ngx_http_core_module.html#root): Point this to `web` directory of eZ Platform installation.
   - [fastcgi_pass](http://nginx.org/en/docs/http/ngx_http_fastcgi_module.html#fastcgi_pass): Socket or TCP address of `php-fpm`.
2. Copy `ez_params.d` directory to folder where you placed virtualhost file, for examples:
   - Debian/Ubuntu: `sudo cp -R doc/nginx/ez_params.d /etc/nginx/sites-enabled/`
   - RHEL/CentOS/Amazon-Linux: `sudo cp -R doc/nginx/ez_params.d /etc/nginx/conf.d/`
3. Restart Nginx, normally: `sudo service nginx restart`

Example config:

    server {
        listen       80;
        # Further documentation: http://nginx.org/en/docs/http/server_names.html
        server_name  localhost;

        root /var/www/ezplatform/web;

        # Additional Assetic rules for environments different from dev,
        # remember to run php app/console assetic:dump --env=prod
        # and make sure to comment these out in "dev" environment.
        include ez_params.d/ez_prod_rewrite_params;

        # Access to repository images in single server setup 
        include ez_params.d/ez_rewrite_params;

        # upload max size
        client_max_body_size 40m;

        location / {
            location ~ ^/app\.php(/|$) {
                include ez_params.d/ez_fastcgi_params;

                # FPM socket
                # Possible values : unix:/var/run/php5-fpm.sock or 127.0.0.1:9000
                fastcgi_pass 127.0.0.1:9000;

                # FPM fastcgi_read_timeout
                fastcgi_read_timeout 90s;

                # Environment.
                # Possible values: "prod" and "dev" out-of-the-box, other values possible with proper configuration
                # Make sure to comment the "ez_params.d/ez_prod_rewrite_params" include above in dev.
                # Defaults to "prod" if omitted
                fastcgi_param SYMFONY_ENV prod;
            }
        }

        include ez_params.d/ez_server_params;
    }


Virtual host template
---------------------
This folder contains `vhost.template` file which provides more features you can enable in your virtual host configuration.

Bash script *(Unix/Linux/OS X)* exists to generate the configuration. To display the help text, execute the following command from the eZ install root:
```bash
./bin/vhost.sh -h
```
