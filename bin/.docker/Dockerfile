FROM alpine:3.3

MAINTAINER eZ Systems AS "engineering@ez.no"

# Add custom ez user, as used by php image
# In Alpine, user xfs has UID/GID 33 as Debian's www-data, so we add it's group as primary group of ez.
RUN addgroup -g 10000 ez \
 && adduser -g xfs -G ez -u 10000 -h /home/ez -D ez

# Copy in project files and make it work dir
COPY . /var/www
WORKDIR /var/www

# Make sure cache is wiped, owner is set to ez and that www-data has access.
RUN rm -Rf app/logs/* app/cache/*/* \
 && chown ez:ez -R /var/www \
 && find app/cache app/logs web -type d | xargs --no-run-if-empty chmod -R 2775 \
 && find app/cache app/logs web -type f | xargs --no-run-if-empty chmod -R 664 \
 && chown :xfs -R app/cache app/logs web

# Declare volumes so it an can be shared with other containers
VOLUME [ "/var/www" ]
VOLUME [ "/var/www/web/var" ]

CMD ["/bin/chmod", "g+s", "/var/www/web/var"]
