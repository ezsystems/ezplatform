#!/bin/sh

# vhost & fastcgi setup
sudo php bin/.travis/generatevhost.php \
         --basedir=$TRAVIS_BUILD_DIR \
         --env=behat \
         doc/apache2/vhost.template \
         /etc/apache2/sites-available/behat
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
listen = 127.0.0.1:9000
pm = static
pm.max_children = 2

php_admin_value[memory_limit] = 256M
" > ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf

sudo echo 'date.timezone = "Europe/Oslo"' >> ~/.phpenv/versions/$TRAVIS_PHP_VERSION/etc/conf.d/travis.ini
sudo echo "cgi.fix_pathinfo = 1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini

# restart
echo "> restart FPM"
sudo ~/.phpenv/versions/$(phpenv version-name)/sbin/php-fpm
echo "> restart apache2"
sudo service apache2 restart
