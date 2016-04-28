FROM alpine:3.3

MAINTAINER eZ Systems AS "engineering@ez.no"

# Add custom ez user, as used by php image
RUN adduser -u 10000 -h /home/ez -D -s /sbin/nologin ez ez

# Copy in project files and make it work dir
COPY . /var/www
WORKDIR /var/www

# Make sure cache is wiped, owner is set to ez and that www-data has access.
RUN rm -Rf app/logs/* app/cache/*/* .git/ web/var \
 && mkdir web/var \
 && chown ez:ez -R /var/www

# Declare volumes so it an can be shared with other containers
VOLUME /var/www /var/www/web/var

CMD ["/bin/true"]
