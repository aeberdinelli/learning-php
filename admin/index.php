<?php
define('included', true);

include "../includes/global.php";
include "../includes/utils.php";
include "../includes/idioma.php";
include "../includes/template.php";
include "../includes/usuario.php";

$idioma     = new Idioma("../idiomas");
$template   = new Template("admin", "../templates");
$usuario    = new Usuario();

$template->preparar("index");
$template->actualizar(array(
    'url'   => $_GET['route']
));
$template->generar();
?>
