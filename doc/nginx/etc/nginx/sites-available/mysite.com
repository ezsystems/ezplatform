server {
    # Port to listen to, example:
    # listen       80;
    listen       %PORT%;
    # Host and alias to respond to, example:
    # server_name  ez.no www.ez.no;
    # Further documentation: http://nginx.org/en/docs/http/server_names.html
    server_name  %HOST% %HOST_ALIAS%;

    root %BASEDIR%/web;

    # Legacy example
    # root %BASEDIR%/ezpublish_legacy;

    # Additional Assetic rules for eZ Publish 5.1 / 2013.4 and higher.
    ## Don't forget to run php ezpublish/console assetic:dump --env=prod
    ## and make sure to comment these out in DEV environment.
    include ez_params.d/ez_prod_rewrite_params;

    # ezlegacy cluster rewrite rules, uncomment if vhost uses DFS clustering
    # For 5.4+:
    #include ez_params.d/ez_cluster_rewrite_params;
    # For 5.3:
    #include ez_params.d/5.3_cluster/ez_cluster_rewrite_params;

    # ez rewrite rules
    include ez_params.d/ez_rewrite_params;

    # upload max size
    client_max_body_size 2m;

    location / {
        location ~ ^/(index|index_(rest|cluster|treemenu_tags))\.php(/|$) {
            include ez_params.d/ez_fastcgi_params;

            # FPM socket
            fastcgi_pass unix:/var/run/php5-fpm.sock;
            # FPM network
            #fastcgi_pass 127.0.0.1:9000;

            ## eZ Publish ENVIRONMENT variables, used for customizing index.php execution (not used by console commands)

            # Environment.
            # Possible values: "prod" and "dev" out-of-the-box, other values possible with proper configuration
            # Make sure to comment the "ez_params.d/ez_prod_rewrite_params" include above in dev.
            # Defaults to "prod" if omitted
            #fastcgi_param ENVIRONMENT dev;

            # Whether to use custom ClassLoader (autoloader) file
            # Needs to be a valid path relative to root web/ directory
            # Defaults to bootstrap.php.cache, or autoload.php in debug, supported on 2015.01 and higher
            #fastcgi_param CUSTOM_CLASSLOADER_FILE "../ezpublish/autoload.php";

            # Whether to use Symfony's ApcClassLoader.
            # Possible values: 0 or 1
            # Defaults to 0 if omitted, supported on 5.2 and higher
            #! Not supported as of 2015.01, use CUSTOM_CLASSLOADER_FILE instead for this.
            #fastcgi_param USE_APC_CLASSLOADER 0;

            # Prefix used when USE_APC_CLASSLOADER is set to 1.
            # Use a unique prefix in order to prevent cache key conflicts
            # with other applications also using APC.
            # Defaults to "ezpublish" if omitted, supported on 5.2 and higher
            #! Not supported as of 2015.01, use CUSTOM_CLASSLOADER_FILE instead for this.
            #fastcgi_param APC_CLASSLOADER_PREFIX "ezpublish";

            # Whether to use debugging.
            # Possible values: 0 or 1
            # Defaults to 0 if omitted, unless ENVIRONMENT is set to: "dev", supported on 5.2 and higher
            #fastcgi_param USE_DEBUGGING 0;

            # Whether to use Symfony's HTTP Caching.
            # Disable it if you are using an external reverse proxy (e.g. Varnish)
            # Possible values: 0 or 1
            # Defaults to 1 if omitted, unless ENVIRONMENT is set to: "dev", supported on 5.2 and higher
            #fastcgi_param USE_HTTP_CACHE 1;

            # Whether to use custom HTTP Cache class if USE_HTTP_CACHE is enabled
            # Value must be na autoloadable cache class
            # Defaults to "EzPublishCache", supported on 2015.01 and higher
            #fastcgi_param HTTP_CACHE_CLASS "\Vendor\Project\MyCache";

            # Defines the proxies to trust.
            # Separate entries by a comma
            # Example: "proxy1.example.com,proxy2.example.org"
            # By default, no trusted proxies are set, supported on 5.2 and higher
            #fastcgi_param TRUSTED_PROXIES "%PROXY%";
        }
    }

    # Custom logs
    # access_log %BASEDIR%/ezpublish/logs/httpd-access.log;
    # error_log  %BASEDIR%/ezpublish/logs/httpd-error.log notice;

    include ez_params.d/ez_server_params;
}

