FROM ezsystems/php:7.0-v0

MAINTAINER eZ Systems AS "engineering@ez.no"

# Build argument about keeping auth.json or not (by default on as prod images should'nt get updates via composer update)
ARG REMOVE_AUTH=1

# This is prod image (for dev use just mount your application as host volume into php image we extend here)
ENV SYMFONY_ENV=prod

# Copy in project files into work dir
COPY . /var/www

# Do composer install, remove cache, fix owner, and remove auth.json if REMOVE_AUTH=1
RUN composer install --optimize-autoloader --no-progress --no-interaction --prefer-dist \
 && rm -Rf app/logs/* app/cache/*/* .git/ web/var \
 && mkdir web/var \
 && chown ez:ez -R /var/www \
 && [ "$REMOVE_AUTH" = "1" ] && rm -f auth.json

# Declare volumes so it an can be shared with other containers
# Also since run.sh will use setfacl, and that does not work on aufs (but volumes does not use that)
VOLUME /var/www /var/www/web/var

EXPOSE 9000

CMD /scripts/run.sh
