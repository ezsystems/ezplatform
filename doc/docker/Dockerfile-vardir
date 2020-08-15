FROM busybox

COPY ./public/var /var/www/public/var

WORKDIR /var/www

# Fix permissions for www-data
RUN chown -R www-data:www-data public/var \
    && find public/var -type d -print0 | xargs -0 chmod -R 775 \
    && find public/var -type f -print0 | xargs -0 chmod -R 664

VOLUME ["/var/www/public/var"]

CMD ["/bin/true"]
