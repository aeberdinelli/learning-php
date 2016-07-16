<?php
define('included', true);

include "includes/global.php";
include "includes/utils.php";

include "includes/idioma.php";
include "includes/template.php";
include "includes/plugins.php";
include "includes/pagina.php";

$plugins = new Plugins();
$idioma = new Idioma();
$template = new Template('default');
$pagina = new Pagina();

$route = (isset($_GET['route'])) ? parse('url', $_GET['route']) : '';

if ($route == '' || $route == 'index.php')
{
    $template->preparar("index");
    $template->generar();
}

$pagina->cargar();
?>
