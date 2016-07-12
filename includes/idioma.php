<?php
if (!defined('included'))
{
    die;
}

class Idioma
{
    private $carpeta = 'idiomas';
    private $frases = array();
    private $diccionario = array();

    public $defecto = idioma_default;
    public $actual = idioma_default;

    public $soportados = array();

    public function __construct($idioma = '', $folder = '')
    {
        if ($folder != '')
        {
            $this->carpeta = $folder;
        }

        $fp = opendir($this->carpeta);
        while ($f = readdir($fp))
        {
            if (extension($f) == 'json')
            {
                $soportados[] = str_replace('.json', '', $f);
            }
        }

        if ($idioma == '')
        {
            if (isset($_COOKIE['lang']))
            {
                $idioma = htmlentities($_COOKIE['lang'], ENT_QUOTES);
            }
            else
            {
                // Detectar idioma
                $idioma = $this->detectar_idioma();
            }
        }

        $this->actual = $idioma;
        if (file_exists($this->carpeta.'/'.$idioma.'.json'))
        {
            $this->frases = json_decode(file_get_contents($this->carpeta.'/'.$idioma.'.json'), true);
        }
        else
        {
            throw new Exception("No se pudo obtener el archivo de idioma ".$this->carpeta.'/'.$idioma.'.json');
        }
    }

    public function parse_vars($txt = '')
    {
        if ($txt != '')
        {
            preg_match_all('/\[\[idioma\.([a-zA-Z0-9\.\-_]+)\]\]/', $txt, $var, PREG_PATTERN_ORDER);

            for ($i = 0;$i < count($var[1]);$i++)
            {
                $txt = str_replace($var[0][$i], $this->get($var[1][$i]), $txt);
            }
        }

        return $txt;
    }

    // For paypal locale
    public function get_locale()
    {
        $locale = 'US';

        switch ($this->actual)
        {
            case 'en':
                $locale = 'US';
            break;
            case 'br':
            case 'pr':
                $locale = 'BR';
            break;
            default:
                $locale = strtoupper($this->actual);
        }

        return $locale;
    }

    // Forzar la utilizacion de un idioma
    public function set($idioma)
    {
        $this->actual = $idioma;
        $this->frases = array();

        if (!in_array($idioma, $this->soportados))
        {
            throw new Exception("El idioma {$idioma} no está soportado");
            return false;
        }

        if (file_exists($this->carpeta.'/'.$idioma.'.json'))
        {
            $this->frases = json_decode(file_get_contents($this->carpeta.'/'.$idioma.'.json'), true);
        }
        else
        {
            throw new Exception("No se pudo cargar el archivo de idioma ".$this->carpeta."/".$idioma.".json");
        }

        // Recordar idioma
        setcookie('lang', $idioma, time()+60*60*24*365, '/');
    }

    public function get($frase)
    {
        $trozos = explode(".", $frase);
        $f = '';

        foreach ($trozos as $trozo)
        {
            if (is_array($f))
            {
                $f = $f[$trozo];
            }
            else
            {
                if (isset($this->frases[$trozo]))
                {
                    $f = $this->frases[$trozo];
                }
                else
                {
                    throw new Exception('La frase "'.$frase.'" no esta definida en el idioma '.$this->idioma_actual.'.');
                }
            }
        }

        $argumentos = func_get_args();
        for ($i = 1;$i < count($argumentos);$i++)
        {
            $f = str_replace("%".($i), $argumentos[$i], $f);
        }

        // Borrar escapes \1, \2, etc
        return preg_replace('#%\\\([0-9]+)#', '%\\1', $f);
    }

    public function detectar_idioma()
    {
        $idioma = $this->defecto;

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
        {
            $pos = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));

            if ($pos != '' && in_array($pos, $this->soportados))
            {
                $idioma = $pos;
            }
        }

        return $idioma;
    }
}
