<?php
if (!defined('included'))
{
    die;
}

class Pagina
{
    public function cargar($page = '')
    {
        global $db, $template, $plugins;

        $plugins->ejecutar("empieza_carga_pagina");

        $url = ($page == '') ? $_GET['route'] : $page;
        $query = '';

        if ($url == '' || $url == '/' || $url == 'index.php')
        {
            $plugins->ejecutar("pagina_muestra_index");
            $template->preparar("index");
            $template->generar();
        }

        if (preg_match('/^[0-9]+$/', $url))
        {
            $query = " id = '".parse('int', $url)."' ";
        }
        else
        {
            $url = parse('text-safe', $url);
            $query = " url = '{$url}' ";
        }

        $sql = $db->query("
            SELECT
                titulo,
                body,
                tipo,
                DATE_FORMAT(updated,'%d/%m/%Y')
            FROM `pages`
            WHERE
                {$query}
            LIMIT 1
        ");

        if ($pagina = $sql->fetch_object())
        {
            $body = $pagina->body;

            $plugins->ejecutar("pagina_obtenida");

            if ($pagina->tipo == 'php')
            {
                eval($body);
            }
            else
            {
                $template->preparar("page");

                $template->actualizar(array(
                    'body'      => $body,
                    'titulo'    => $pagina->titulo
                ));

                $plugins->ejecutar("empiza_pagina");
                $template->generar();

                $plugins->ejecutar("termina_pagina");
            }
        }
        else
        {
            $plugins->ejecutar("pagina_error_404");

            $template->preparar("404");
            $template->actualizar(array(
                'url'   => $_GET['route']
            ));

            $template->generar();
        }
    }
}
?>
