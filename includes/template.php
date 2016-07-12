<?php
if (!defined('included'))
{
    die;
}

class Template
{
    private $carpeta = 'templates';
    private $base = '';
    private $html = '';
    private $reemplazos = array();
    private $scripts = '';
    private $base_url = '';
    private $code = 'function __template() { ?>';

    public $cargar_variables = true;
    public $cargar_idioma = true;
    public $usar_includes = true;
    public $eval_code = true;

    public function __construct($template, $folder = '')
    {
        if ($folder != '')
        {
            $this->carpeta = $folder;
        }

        if (defined('root_url'))
        {
            $this->base_url = root_url;
        }

        $this->carpeta .= '/'.$template;
    }

    public function ejecutar($js)
    {
        $this->scripts .= '<script>'.$js.'</script>';
    }

    public function preparar($archivo, $incluir = true)
    {
        $buffer = '';
        $archivos = array();

        if (!is_array($archivo))
        {
            $archivos[] = $archivo;
        }
        else
        {
            $archivos = $archivo;
        }

        foreach ($archivos as $file)
        {
            if ($g = file_get_contents($this->carpeta.'/'.$file.'.html'))
            {
                if ($incluir)
                {
                    $this->html .= $g;
                }
                else
                {
                    $buffer .= $g;
                }
            }
            else
            {
                // Error
                echo "No existe ".$this->carpeta.'/'.$file.'.html<br><br>';
            }
        }

        return ($incluir) ? true : $buffer;
    }

    public function parse_pseudo()
    {
        $this->code .= $this->html;

        $this->code = str_replace('<!-- if ', '<?php if (', $this->code);
        $this->code = str_replace('<!-- else -->', '<?php } else { ?>', $this->code);
        $this->code = str_replace('<!-- end if -->', '<?php } ?>', $this->code);
        $this->code = str_replace(' then -->',') { ?>', $this->code);
    }

    public function actualizar($var, $archivo = '')
    {
        if ($archivo != '')
        {
            $html = $this->preparar($archivo, false);

            foreach ($var as $variable => $valor)
            {
                $html = str_replace('[['.$variable.']]', $valor, $html);
            }

            return $html;
        }

        foreach ($var as $variable => $valor)
        {
            $this->reemplazos[$variable] = $valor;
        }

        return true;
    }

    public function cargar_bloque($archivo, $variables)
    {
        return $this->actualizar($variables, $archivo);
    }

    public function cargar_idioma($html = '')
    {
        global $idioma;

        if ($html != '')
        {
            preg_match_all('/\[\['.idioma_var.'\.([a-zA-Z0-9\.\-_]+)\]\]/', $html, $var, PREG_PATTERN_ORDER);

            for ($i = 0;$i < count($var[1]);$i++)
            {
                $html = str_replace($var[0][$i], $idioma->get($var[1][$i]), $html);
            }

            return $html;
        }

        preg_match_all('/\[\['.idioma_var.'\.([a-zA-Z0-9\.\-_]+)\]\]/', $this->html, $var, PREG_PATTERN_ORDER);

        for ($i = 0;$i < count($var[1]);$i++)
        {
            $this->html = str_replace($var[0][$i], $idioma->get($var[1][$i]), $this->html);
        }

        return true;
    }

    public function generar($return = false, $eval = true)
    {
        $this->reemplazos['template_base'] = $this->base;

        if ($this->usar_includes)
        {
            preg_match_all("/<!-- \[include ([a-zA-Z0-9\.\-_]+)\] -->/", $this->html, $resultados, PREG_PATTERN_ORDER);

            foreach ($resultados[1] as $archivo)
            {
                $this->html = str_replace("<!-- [include ".$archivo."] -->", file_get_contents($this->carpeta.'/'.$archivo), $this->html);
            }
        }

        if ($this->cargar_variables)
        {
            foreach ($this->reemplazos as $buscar => $reemplazar)
            {
                $this->html = str_replace('[['.$buscar.']]', $reemplazar, $this->html);
            }
        }

        if ($this->cargar_idioma)
        {
            $this->cargar_idioma();
        }

        // Fix base url
        $fix = array(
            'src="/'        => 'src="'.$this->base_url.'/',
            'href="/'       => 'href="'.$this->base_url.'/',
            'action="/'     => 'action="'.$this->base_url.'/'
        );

        $this->html = str_replace(array_keys($fix), array_values($fix), $this->html);
        $this->html .= $this->scripts;

        $this->parse_pseudo();

        $this->code .= "<?php } ?>";

        eval($this->code);

        if ($return)
        {
            if ($eval)
            {
                echo highlight_string('<?php '.$this->code);
            }
            else
            {
                echo $this->html;
            }
        }
        else
        {
            if (function_exists('__template'))
            {
                __template();
            }
            else
            {
                throw new Exception("No se pudo generar el template");
            }
        }
    }

    public static function redirigir($url)
    {
        if (substr($url, 0, 1) == "/")
        {
            $url = root_url.$url;
        }

        if (!headers_sent())
        {
            header("Location: {$url}");
        }
        else
        {
            echo '<script>window.location = "'.$url.'";</script>';
        }

        die;
    }
}
?>
