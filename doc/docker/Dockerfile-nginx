ARG PHP_IMAGE=ezsystems/php:7.3-v1
FROM ${PHP_IMAGE}-node as web-build

ENV APP_ENV=prod

# Copy in project files into work dir
COPY . /var/www

# Create asset directories that might not exists
RUN if [ ! -d /var/www/public/bundles ]; then mkdir /var/www/public/bundles; fi
RUN if [ ! -d /var/www/public/css ]; then mkdir /var/www/public/css; fi
RUN if [ ! -d /var/www/public/fonts ]; then mkdir /var/www/public/fonts; fi
RUN if [ ! -d /var/www/public/js ]; then mkdir /var/www/public/js; fi
RUN if [ ! -d /var/www/public/assets ]; then mkdir /var/www/public/assets; fi

# Generate assets using hard copy as we need to copy them over to resulting image
RUN composer config extra.symfony-assets-install hard
RUN composer run-script auto-scripts --no-interaction


# Copy over just the files we want in second stage, so resulting stage only has assets
# and vhost config in as few layers as possible
FROM nginx:stable as web-multilayers

COPY bin/vhost.sh /var/www/bin/vhost.sh
COPY doc/nginx/vhost.template /var/www/doc/nginx/vhost.template

# Auto generated assets
COPY --from=web-build /var/www/public/bundles /var/www/public/bundles
COPY --from=web-build /var/www/public/css /var/www/public/css
COPY --from=web-build /var/www/public/fonts /var/www/public/fonts
COPY --from=web-build /var/www/public/js /var/www/public/js

# User provided assets
COPY --from=web-build /var/www/public/assets /var/www/public/assets


# In third stage build the resulting image
FROM nginx:stable

COPY --from=web-multilayers /var/www /var/www
COPY doc/nginx/ez_params.d /etc/nginx/ez_params.d

CMD /bin/bash -c "cd /var/www && bin/vhost.sh --template-file=doc/nginx/vhost.template > /etc/nginx/conf.d/default.conf && nginx -g 'daemon off;'"
