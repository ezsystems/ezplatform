<?php
if (is_file($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$_SERVER['SCRIPT_NAME'])) {
    return false;
}

putenv('SYMFONY_ENV=behat');
$_SERVER = array_merge($_SERVER, $_ENV);
$_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'app.php';

require 'app.php';
