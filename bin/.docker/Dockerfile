# TMP not in use yet
FROM ezsystems/php:7.0

COPY . /var/www

RUN composer install --no-progress --no-interaction --prefer-dist

CMD /run.sh

EXPOSE 9000
