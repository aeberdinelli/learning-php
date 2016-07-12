<?php
define('included', true);

include "includes/global.php";
include "includes/utils.php";
include "includes/idioma.php";
include "includes/template.php";

$idioma = new Idioma();
$template = new Template('default');

$template->preparar('index');
$template->generar();
?>
