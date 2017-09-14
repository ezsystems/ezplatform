FROM ezsystems/php:7.0-v1

# This is prod image (for dev use just mount your application as host volume into php image we extend here)
ENV SYMFONY_ENV=prod

# Copy in project files into work dir
COPY . /var/www

# Check for ignored folders to avoid layer issues, ref: https://github.com/docker/docker/issues/783
RUN if [ -d .git ]; then echo "ERROR: .dockerignore folders detected, exiting" && exit 1; fi

# Install and prepare install
RUN mkdir -p web/var \
    # For now, only run composer in order to generate parameters.yml
    && composer run-script post-install-cmd --no-interaction \
    && composer dump-autoload --optimize \
# Clear cache again so env variables are taken into account on startup
    && rm -Rf app/logs/* app/cache/*/* \
# Fix permissions for www-data
    && chown -R www-data:www-data app/cache app/logs web/var \
    && find app/cache app/logs web/var -type d -print0 | xargs -0 chmod -R 775 \
    && find app/cache app/logs web/var -type f -print0 | xargs -0 chmod -R 664 \
# Remove var dir from image ( future : use multi-stage-builds to avoid var/ from ever reaching a layer and dereby use disk space )
    && rm -rf var \
# Remove composer cache to avoid it taking space in image
    && rm -rf ~/.composer/*/*

