#!/bin/sh

# vhost & fastcgi setup
sed s?%basedir%?$TRAVIS_BUILD_DIR? bin/.travis/apache2/behat_vhost | sudo tee /etc/apache2/sites-available/behat > /dev/null
sudo cp bin/.travis/apache2/php5-fcgi /etc/apache2/conf.d/php5-fcgi

# modules enabling
sudo a2enmod rewrite actions fastcgi alias

# sites disabling & enabling
sudo a2dissite default
sudo a2ensite behat

# FPM
USER=$(whoami)

sudo echo "
[global]

[www]
user = $USER
group = $USER
listen = /tmp/php-fpm.sock
pm = static
pm.max_children = 2

php_admin_value[memory_limit] = 256M
" > ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf

sudo echo 'date.timezone = "Europe/Oslo"' >> ~/.phpenv/versions/$TRAVIS_PHP_VERSION/etc/conf.d/travis.ini
sudo echo "cgi.fix_pathinfo = 1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini

# restart
sudo ~/.phpenv/versions/$(phpenv version-name)/sbin/php-fpm
sudo service apache2 restart
