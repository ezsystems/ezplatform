FROM busybox

COPY ./web/var /var/www/web/var

WORKDIR /var/www

# Fix permissions for www-data
RUN chown -R www-data:www-data web/var \
    && find web/var -type d -print0 | xargs -0 chmod -R 775 \
    && find web/var -type f -print0 | xargs -0 chmod -R 664

VOLUME ["/var/www/web/var"]

CMD ["/bin/true"]
