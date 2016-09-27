<?php

require_once '../vendor/autoload.php';

define('FHCENV', 'PROD');

if(FHCENV != 'DEV'){
    error_reporting(~E_ALL & ~E_NOTICE & ~E_WARNING);
}

define('DS', DIRECTORY_SEPARATOR);

$include_path = realpath(dirname(__FILE__). DS. '..'.DS);
define('APPATH', $include_path );

set_include_path(get_include_path() . PATH_SEPARATOR . $include_path);


\Fhc\Bootstrap\Load::config('config/mt4.ini');
