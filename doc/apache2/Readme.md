eZ Publish Apache 2.2 / 2.4  configuration
=========================================

For information on which version of Apache is supported with your version of eZ Publish, consult with the online documentation on http://doc.ez.no.


Prerequisite
------------
- Apache 2.x must be installed in prefork mode
- Modules enabled: `mode_php`, `mod_rewrite`, `mod_setenvif` and optionally `mod_expires` for improved performance in production.


Configuration
------------
- Copy the provided example file `etc/apache2/vhost.template` into target folder like `/etc/apache2/sites-available/yoursite.com`
-- Note that `apache2` target folder name on RHEL/Centos is `httpd`!
- Edit it and adapt the configuration to suit your needs
-- Make sure to replace all `%VARIABLES%`, further description below
-- For cluster setup, enable custom rewrite rules for this found inline in the vhost.tempalte
-- In 5.2 and higher you can optionally configure the eZ Publish front controller (`index.php`) using environment variables documented inline in the vhost template.
-- Adapt the ´<Directory´ section for your Apache version
- Create a symlink of /etc/apache2/sites-available/yoursite.com into /etc/apache2/sites-enabled/yoursite.com
- restart Apache

#### vhost.template %VARIABLES%

| Name         | Description          |
|--------------|----------------------|
| %IP_ADDRESS% | The IP address of the virtual host, for example "128.39.140.28". Apache allows the usage of a wildcard (`*`) , either for just ip like `<VirtualHost *:%PORT%>` or for both using `<VirtualHost *>` |
| %PORT%       | The port on which the web server listens for incoming requests. This is an optional setting, the default port for http traffic is 80. |
| %HOST%       | The host(/IP address) that Apache should use to match this virtual host config. |
| %HOST_ALIAS% | Additional comma separated list of hosts(/IP addresses) that Apache should use to match this virtual host config. |
| %BASEDIR%    | Full path to eZ Publish, for example "/var/www/ezpublish-5.3.0", where "web" directory and rest of eZ Publish 5.x exists. |
| %ENV%        | eZ Publish (Symfony) environment, isolation of cache and config for different use cases, out of the box: `prod` or `dev`. |
| %PROXY%      | Optional, needs to be enabled in your vhost file. Defines the proxies to trust to get access to ESI resources and not be treated as remote IP. |

#### Pure legacy setup

In eZ Publish 5.x you can optionally set it up to only use legacy, this is useful for 4.x upgrades which only uses legacy.
*Warning: By doing this, absolutely no integrations between legacy and Platform works, so you can not use any of the Platform features (API, HttpCache, Symfony, ..) in this setup.*

To setup pure legacy you will have to modify the vhost configuration to point to ezpublish_legacy folder as BASEDIR, and remove use of `/web` in the config.
You'll also need to use the cluster rewrite rules for 5.3 and below if using cluster setup.

#### NameVirtualHost conflicts

The "NameVirtualHost" setting might already exist in the default configuration. Defining a new one will result in a conflict. If Apache reports errors such as "NameVirtualHost [IP_ADDRESS] has no VirtualHosts" or "Mixing * ports and non-* ports with a NameVirtualHost address is not supported", try skipping the NameVirtualHost line. See [more info about the NameVirtualHost directive](http://httpd.apache.org/docs/2.2/mod/core.html#namevirtualhost)
