#!/bin/sh

# vhost & fastcgi setup
sed s?%basedir%?$TRAVIS_BUILD_DIR? bin/.travis/apache2/behat_vhost | sudo tee /etc/apache2/sites-available/behat > /dev/null
sudo cp bin/.travis/apache2/php5-fcgi /etc/apache2/conf.d/php5-fcgi

# modules enabling
sudo a2enmod rewrite
sudo a2enmod actions
sudo a2enmod fastcgi

# sites disabling & enabling
sudo a2dissite default
sudo a2ensite behat

# FPM
DIR=$(dirname "$0")
PHP_FPM_BIN="$HOME/.phpenv/versions/$TRAVIS_PHP_VERSION/sbin/php-fpm"
PHP_FPM_CONF="$DIR/php-fpm.conf"
USER=$(whoami)

echo "
[global]

[travis]
user = $USER
group = $USER
listen = /tmp/php-fpm.sock
pm = static
pm.max_children = 2

php_admin_value[memory_limit] = 256M
" > $PHP_FPM_CONF

echo 'date.timezone = "Europe/Oslo"' >> ~/.phpenv/versions/$TRAVIS_PHP_VERSION/etc/conf.d/travis.ini

# restart
sudo $PHP_FPM_BIN --fpm-config "$PHP_FPM_CONF"
sudo service apache2 restart
