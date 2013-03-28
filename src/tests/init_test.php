<?php

ini_set('display_errors', 1);

define('APP_ROOT_DIRECTORY', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR);

// Include the composer autoloader.
$autoloader = require_once APP_ROOT_DIRECTORY . 'vendor/autoload.php';

?>
