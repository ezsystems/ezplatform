FROM ezsystems/php:7.0-v0

MAINTAINER eZ Systems AS "engineering@ez.no"

# Build argument about keeping auth.json or not (by default on as prod images should'nt get updates via composer update)
ARG REMOVE_AUTH=1

# This is prod image (for dev use just mount your application as host volume into php image we extend here)
ENV SYMFONY_ENV=prod

# Copy in project files into work dir
COPY . /var/www

# Remove cache folders to avoid layer issues, ref: https://github.com/docker/docker/issues/783
RUN rm -Rf app/logs/* app/cache/* .git/* \
 && mkdir -p web/var \
 && composer install --optimize-autoloader --no-progress --no-interaction --prefer-dist \
# Clear cache again so env variables are taken into account on startup
 && rm -Rf app/logs/* app/cache/*/* \
# Fix permissions for www-data
 && chown -R www-data:www-data app/cache app/logs web/var \
 && find app/cache app/logs web/var -type d | xargs chmod -R 775 \
 && find app/cache app/logs web/var -type f | xargs chmod -R 664 \
 && [ "$REMOVE_AUTH" = "1" ] && rm -f auth.json

# Declare volumes so it an can be shared with other containers
# Also since run.sh will use setfacl, and that does not work on aufs (but volumes does not use that)
VOLUME /var/www /var/www/web/var

EXPOSE 9000

CMD /scripts/run.sh
