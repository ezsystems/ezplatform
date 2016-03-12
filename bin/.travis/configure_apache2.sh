#!/bin/bash

# vhost & fastcgi setup
./bin/vhost.sh \
        --basedir=$TRAVIS_BUILD_DIR \
        --sf-env=behat \
        --sf-debug=1 \
        --template-file=doc/apache2/vhost.template \
    | sudo tee /etc/apache2/sites-enabled/behat.conf > /dev/null

CODENAME=$(lsb_release -c)
if [ "$CODENAME" == "Codename:	trusty" ] ; then
    sudo cp bin/.travis/apache2/php5-fcgi_apache24 /etc/apache2/conf-enabled/php5-fcgi.conf
    sudo a2dissite 000-default
else
    sudo cp bin/.travis/apache2/php5-fcgi /etc/apache2/conf.d/php5-fcgi.conf
    sudo a2dissite default
fi

# modules enabling
sudo a2enmod rewrite actions fastcgi alias

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
