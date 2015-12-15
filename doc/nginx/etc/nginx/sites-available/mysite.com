server {
    # Port to listen to, example:
    # listen       80;
    listen       %PORT%;
    # Host and alias to respond to, example:
    # server_name  ez.no www.ez.no;
    # Further documentation: http://nginx.org/en/docs/http/server_names.html
    server_name  %HOST% %HOST_ALIAS%;

    root %BASEDIR%/web;

    # Additional Assetic rules for eZ Publish 5.1 / 2013.4 and higher.
    ## Don't forget to run php app/console assetic:dump --env=prod
    ## and make sure to comment these out in DEV environment.
    include ez_params.d/ez_prod_rewrite_params;

    # Cluster/streamed files rewrite rules. Enable on cluster with DFS as a binary data handler
    #rewrite "^/var/([^/]+/)?storage/images(-versioned)?/(.*)" "/app.php" break;

    # ez rewrite rules
    include ez_params.d/ez_rewrite_params;

    # upload max size
    client_max_body_size 20m;

    location / {
        location ~ ^/app\.php(/|$) {
            include ez_params.d/ez_fastcgi_params;

            # FPM socket
            fastcgi_pass unix:/var/run/php5-fpm.sock;
            # FPM network
            #fastcgi_pass 127.0.0.1:9000;

            ## eZ Platform ENVIRONMENT variables, used for customizing app.php execution (not used by console commands)

            # Environment.
            # Possible values: "prod" and "dev" out-of-the-box, other values possible with proper configuration
            # Make sure to comment the "ez_params.d/ez_prod_rewrite_params" include above in dev.
            # Defaults to "prod" if omitted
            #fastcgi_param SYMFONY_ENV dev;

            # Whether to use custom ClassLoader (autoloader) file
            # Needs to be a valid path relative to root web/ directory
            # Defaults to bootstrap.php.cache, or autoload.php in debug
            #fastcgi_param SYMFONY_CLASSLOADER_FILE "../app/autoload.php";

            # Whether to use debugging.
            # Possible values: 0 or 1
            # Defaults to 0 if omitted, unless SYMFONY_ENV is set to: "dev"
            #fastcgi_param SYMFONY_DEBUG 0;

            # Whether to use Symfony's HTTP Caching.
            # Disable it if you are using an external reverse proxy (e.g. Varnish)
            # Possible values: 0 or 1
            # Defaults to 1 if omitted, unless SYMFONY_ENV is set to: "dev"
            #fastcgi_param SYMFONY_HTTP_CACHE 1;

            # Whether to use custom HTTP Cache class if SYMFONY_HTTP_CACHE is enabled
            # Value must be na autoloadable cache class
            # Defaults to "AppCache"
            #fastcgi_param SYMFONY_HTTP_CACHE_CLASS "\Vendor\Project\MyCache";

            # Defines the proxies to trust.
            # Separate entries by a comma
            # Example: "proxy1.example.com,proxy2.example.org"
            # By default, no trusted proxies are set
            #fastcgi_param SYMFONY_TRUSTED_PROXIES "%PROXY%";
        }
    }

    # Custom logs
    # access_log %BASEDIR%/app/logs/httpd-access.log;
    # error_log  %BASEDIR%/app/logs/httpd-error.log notice;

    include ez_params.d/ez_server_params;
}

