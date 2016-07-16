<?php
if (!defined('included'))
{
    die;
}

class Plugins
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
            $this->activadas[] = $extension->nombre;
            $this->errores[$extension->nombre] = array();

            try
            {
                include "extensiones/".$extension->carpeta."/".$extension->loader;
                $this->cargadas[] = $extension->nombre;
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

        $condicion = '';
        if (is_array($evento))
        {
            foreach ($evento as $ev)
            {
                if ($condicion != '')
                {
                    $condicion .= ' OR ';
                }

                $condicion .= ' f.evento = "'.$ev.'" ';
            }
        }
        else
        {
            $condicion .= ' f.evento = "'.$evento.'" ';
        }

        $sql = $db->query("
            SELECT
                f.function AS function,
                x.nombre AS extension
            FROM `funciones` AS f
            LEFT JOIN `extensiones` AS x ON(
                x.id = f.extension_id
                AND x.estado = 'habilitada'
                AND (
                    {$condicion}
                )
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

    public function instalar($zip, $activar = true)
    {
        global $db;

        if (!file_exists($zip) || is_readable($zip))
        {
            throw new Exception("La extension que se intenta instalar no existe.");
            return false;
        }

        if (!is_writable("../extensiones/"))
        {
            throw new Exception("La carpeta extensiones/ no tiene permisos de escritura");
            return false;
        }

        $nombre = parse('text-safe', str_replace('.zip', '', strtolower($zip)));
        @mkdir("extensiones/{$nombre}");

        $fp = new ZipArchive;
        if ($fp->open($zip) === true)
        {
            try
            {
                $fp->extractTo("extensiones/{$nombre}/");
                $fp->close();

                $version = '1.0';

                if (file_exists('extensiones/'.$nombre.'/instalar.json'))
                {
                    $pasos = json_decode(file_get_contents('../extensiones/'.$nombre.'/instalar.json'), true);

                    $extension  = $pasos['extension'];
                    $version    = (isset($pasos['version'])) ? $pasos['version'] : '1.0';

                    $this->errores[$extension] = array();

                    if (isset($pasos['copiar']))
                    {
                        foreach ($pasos['copiar'] as $archivo => $url)
                        {
                            try
                            {
                                copy('../extensiones/'.$nombre.'/'.$archivo, $url);
                            }
                            catch (Exception $e)
                            {
                                $this->errores[$extension][] = $e->getMessage();
                            }
                        }
                    }
                }

                $db->query("
                    INSERT INTO `extensiones`
                ");

                return true;
            }
            catch (Exception $e)
            {
                throw new Exception("No se pudo extraer {$zip}");
            }
        }
        else
        {
            throw new Exception("No se puede leer {$zip}");
        }
    }

    public function obtener_errores()
    {
        return $this->errores;
    }

    public function debug()
    {
        global $template;

        $template->ejecutar('
            var d = document.createElement("div");
            d.innerHTML = "<hr>";
            d.innerHTML += "- Extensiones activadas: '.count($this->activadas).'<br>";
            d.innerHTML += "- Extensiones cargadas: '.count($this->cargadas).'<br>";
            d.innerHTML += "- Con errores: No disponible";
            document.body.appendChild(d);
        ');
    }
}
?>
