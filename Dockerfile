FROM ezsystems/php:7.1-v1

# Build argument about keeping auth.json or not (by default on as prod images should'nt get updates via composer update)
ARG REMOVE_AUTH=1

# This is prod image (for dev use just mount your application as host volume into php image we extend here)
ENV SYMFONY_ENV=prod

# Copy in project files into work dir
COPY . /var/www

# Check for ignored folders to avoid layer issues, ref: https://github.com/docker/docker/issues/783
RUN if [ -d .git ]; then echo "ERROR: .dockerignore folders detected, exiting" && exit 1; fi

# Install and prepare install
RUN mkdir -p web/var \
    && composer install --optimize-autoloader --no-progress --no-interaction --no-suggest --prefer-dist \
# Clear cache again so env variables are taken into account on startup
    && rm -Rf app/logs/* app/cache/*/* \
# Fix permissions for www-data
    && chown -R www-data:www-data app/cache app/logs web/var \
    && find app/cache app/logs web/var -type d -print0 | xargs -0 chmod -R 775 \
    && find app/cache app/logs web/var -type f -print0 | xargs -0 chmod -R 664 \
# Remove composer cache to avoid it taking space in image
    && rm -rf ~/.composer/*/* \
    && [ "$REMOVE_AUTH" = "1" ] && rm -f auth.json

# Declare volumes so it an can be shared with other containers
VOLUME /var/www /var/www/web/var
