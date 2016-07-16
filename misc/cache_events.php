<?php
/**
 * Analizar todos los archivos en una carpeta (y subcarpetas)
 * para encontrar todas las llamadas a eventos de $plugins y tener un listado de ellas
 */
 
$base = '../includes';

function analizar($carpeta)
{
    $archivos = array();
    $fp = opendir($carpeta);

    while ($f = readdir($fp))
    {
        if ($f != '.' && $f != '..' && !is_dir($carpeta.'/'.$f))
        {
            if (!is_dir($carpeta.'/'.$f))
            {
                $archivos[] = $carpeta.'/'.$f;
            }
            else
            {
                analizar($carpeta.'/'.$f);
            }
        }
    }

    return $archivos;
}

$archivos = analizar($base);
$llamadas = array();

foreach ($archivos as $archivo)
{
    $llamadas[$archivo] = array();
    $fp = fopen($archivo, 'r');

    $i = 1;

    while (!feof($fp))
    {
        $linea = fgets($fp, 4096);

        preg_match_all('/\$plugins->ejecutar\("([a-zA-Z0-9\-_ ]+)"\)/', $linea, $resultados, PREG_PATTERN_ORDER);

        foreach ($resultados[1] as $evento)
        {
            $llamadas[$archivo][] = array(
                'linea'     => $i,
                'evento'    => $evento
            );
        }

        $i++;
    }
}

foreach ($llamadas as $archivo => $data)
{
    if (count($data) > 0)
    {
        echo "\n### ".$archivo."\n";

        foreach ($data as $evento)
        {
            echo "* ";
            echo "Linea *".$evento['linea']."*: `".$evento['evento']."`\n";
        }
    }
}
?>
