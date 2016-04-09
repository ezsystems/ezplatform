FROM alpine:3.3

MAINTAINER eZ Systems AS "engineering@ez.no"

# Copy in project files
COPY . /var/www

# Declare volumes so it an can be shared with other containers
VOLUME [ "/var/www" ]
VOLUME [ "/var/www/web/var" ]

CMD ["/bin/chmod", "g+s", "/var/www/web/var"]
