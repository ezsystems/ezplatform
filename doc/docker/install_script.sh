#!/bin/bash

# Dumping autoload using --optimize-autoloader to keep performenace on a usable level, not needed on linux host.
# Second chown line:  For dev and behat tests we give a bit extra rights, never do this for prod.

for i in $(seq 1 3); do
    composer install --no-progress --no-interaction --prefer-dist --no-suggest --optimize-autoloader && s=0 && break || s=$? && sleep 1
done
if [ "$s" != "0" ]; then
    echo "ERROR : composer install failed, exit code : $s"
    exit $s
fi
mkdir -p web/var

if [ "${INSTALL_DATABASE}" == "1" ]; then 
    php /scripts/wait_for_db.php
    composer ezplatform-install
    if [ "$SYMFONY_CMD" != '' ]; then
      echo '> Executing' "$SYMFONY_CMD"; php bin/console $SYMFONY_CMD
    fi
    echo 'Dumping database into doc/docker/entrypoint/mysql/2_dump.sql for use by mysql on startup.'
    mysqldump -u $DATABASE_USER --password=$DATABASE_PASSWORD -h $DATABASE_HOST --add-drop-table --extended-insert  --protocol=tcp $DATABASE_NAME > doc/docker/entrypoint/mysql/2_dump.sql
fi;

rm -Rf var/logs/* var/cache/*/*
chown -R www-data:www-data var/cache var/logs web/var
find var/cache var/logs web/var -type d -print0 | xargs -0 chmod -R 775
find var/cache var/logs web/var -type f -print0 | xargs -0 chmod -R 664
chown -R www-data:www-data app/config src
