<?php
if (!defined('included'))
{
    die;
}

define('core_version','1.1.3');

// Config
define('debug', true);
define('secret_key', 'supercalifragilisticoespialidoso');

define('account_cookie', 'user_id');
define('account_reciente', 'is_recent');

define('root_url', 'http://localhost/lithium-core');

define('idioma_default', 'es');
define('idioma_var', '_');

// Base de datos
$db = new mysqli('localhost','root','root','lithium');

// Variables comunes para todas las paginas
if (debug)
{
    error_reporting(-1);
    ini_set("display_errors", true);
}
else
{
    error_reporting(0);
}
?>
