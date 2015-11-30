eZ Platform Apache 2.2 / 2.4  configuration
===========================================

For information on which version of Apache is supported with your version of eZ Platform, consult the online documentation on http://doc.ez.no.


Prerequisites
-------------
- Apache 2.x must be installed in prefork mode
- Modules enabled: `mod_php`, `mod_rewrite`, `mod_setenvif` and optionally `mod_expires` for improved performance in production.


Configuration
------------
- Copy the provided example file `etc/apache2/vhost.template` into target folder like `/etc/apache2/sites-available/yoursite.com`
-- Note that `/etc/apache2` target folder name on RHEL/Centos is `/etc/httpd`!
- Edit it and adapt the configuration to suit your needs
-- Make sure to replace all `%VARIABLES%`, further description below
-- For cluster setup, enable custom rewrite rules for it found inline in the vhost.template
-- In 5.2 and higher you can optionally configure the eZ Platform front controller (`app.php`) using environment variables documented inline in the vhost template.
-- Adapt the ´<Directory>´ section for your Apache version
- Create a symlink of /etc/apache2/sites-available/yoursite.com into /etc/apache2/sites-enabled/yoursite.com
- restart Apache

#### vhost.template %VARIABLES%

| Name         | Description          |
|--------------|----------------------|
| %IP_ADDRESS% | The IP address of the virtual host, for example "128.39.140.28". Apache allows the usage of a wildcard (`*`) , either for just ip like `<VirtualHost *:%PORT%>` or for both using `<VirtualHost *>` |
| %PORT%       | The port on which the web server listens for incoming requests. This is an optional setting, the default port for http traffic is 80. |
| %HOST%       | The host(/IP address) that Apache should use to match this virtual host config. |
| %HOST_ALIAS% | Additional comma separated list of hosts(/IP addresses) that Apache should use to match this virtual host config. |
| %BASEDIR%    | Full path to eZ Platform, for example "/var/www/ezplatform-15.05", where "web" directory and rest of eZ Publish 5.x exists. |
| %ENV%        | eZ Platform (Symfony) environment, isolation of cache and config for different use cases, out of the box: `prod` or `dev`. |
| %PROXY%      | Optional, needs to be enabled in your vhost file. Defines the proxies to trust to get access to ESI resources and not be treated as remote IP. |

#### NameVirtualHost conflicts

The "NameVirtualHost" setting might already exist in the default configuration. Defining a new one will result in a conflict. If Apache reports errors such as "NameVirtualHost [IP_ADDRESS] has no VirtualHosts" or "Mixing * ports and non-* ports with a NameVirtualHost address is not supported", try removing the NameVirtualHost line. See [more info about the NameVirtualHost directive](http://httpd.apache.org/docs/2.4/mod/core.html#namevirtualhost)
