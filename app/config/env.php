<?php
// On Symfony container compilation*, reads parameters from env variables if defined and overrides the yml parameters.
// * For typical use cases like Docker, make sure to recompile Symfony container on run to refresh settings.


if ($value = getenv('SYMFONY__DATABASE_DRIVER')) {
    $container->setParameter('database_driver', $value);
}

if ($value = getenv('SYMFONY__DATABASE_HOST')) {
    $container->setParameter('database_host', $value);
}

if ($value = getenv('SYMFONY__DATABASE_NAME')) {
    $container->setParameter('database_name', $value);
}

if ($value = getenv('SYMFONY__DATABASE_USER')) {
    $container->setParameter('database_user', $value);
}

if ($value = getenv('SYMFONY__DATABASE_PASSWORD')) {
    $container->setParameter('database_password', $value);
}
