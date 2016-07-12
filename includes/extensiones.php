<?php
if (!defined('included'))
{
    die;
}

class Extensiones
{
    private $activadas = array();
    private $cargadas = array();
    private $errores = array();

    public function __construct()
    {
        global $db;

        $sql = $db->query("
            SELECT
                nombre,
                carpeta,
                loader
            FROM `extensiones`
            WHERE
                estado = 'habilitada'
            ORDER BY orden ASC, id ASC
        ");

        while ($extension = $sql->fetch_object())
        {
            $activadas[] = $extension->nombre;
            $errores[$extension->nombre] = array();

            try
            {
                include "extensiones/".$extension->carpeta."/".$extension->loader
                $cargadas[] = $extension->nombre;
            }
            catch (Exception $e)
            {
                $this->errores[$extension->nombre][] = $e->getMessage();
            }
        }
    }

    public function ejecutar($evento)
    {
        global $db;

        $sql = $db->query("
            SELECT
                f.function AS function,
                x.nombre AS extension
            FROM `funciones` AS f
            LEFT JOIN `extensiones` AS x ON(
                x.id = f.extension_id
                AND x.estado = 'habilitada'
            )
            WHERE
                f.estado = 'habilitada'
            ORDER BY f.orden ASC, f.id ASC
        ");

        while ($func = $sql->fetch_object())
        {
            if (!function_exists($func->function))
            {
                $this->errores[$func->extension][] = "La función ".$func->function." no está definida";
                continue;
            }

            call_user_func($func->function());
        }
    }

    public function debug()
    {
        global $template;

        $template->ejecutar('
            var d = document.createElement("div");
            d.innerHTML += "- Extensiones activadas: '.count($this->activadas).'<br>";
            d.innerHTML += "- Extensiones cargadas: '.count($this->cargadas).'<br>";
            d.innerHTML += "- Con errores: No disponible";
            document.body.appendChild(d);
        ');
    }

    public funtion __destruct()
    {
        if (debug)
        {
            $this->debug();
        }
    }
}
?>
