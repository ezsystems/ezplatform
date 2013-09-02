#!/bin/sh

# vhost & phpenv
sed s?%basedir%?$TRAVIS_BUILD_DIR? bin/.travis/apache2/behat_vhost | sudo tee /etc/apache2/sites-available/behat > /dev/null
sudo cp bin/.travis/apache2/phpenv /etc/apache2/conf.d/phpconfig


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

php_admin_value[memory_limit] = 128M
" > $PHP_FPM_CONF

# restart
sudo $PHP_FPM_BIN --fpm-config "$PHP_FPM_CONF"
sudo service apache2 restart
