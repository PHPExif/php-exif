<?php

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

date_default_timezone_set('Europe/Brussels');

require_once __DIR__ . '/../vendor/autoload.php';
/*
require_once realpath(__DIR__ . '/../../autoload.php');
$classLoader = new SplClassLoader('PHPExif', __DIR__ . '/../lib');
$classLoader->register();
 */

define('PHPEXIF_TEST_ROOT', __DIR__);