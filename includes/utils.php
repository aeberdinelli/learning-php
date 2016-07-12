<?php
if (!defined('included'))
{
    die;
}

$regex = array(
    'url'       => '/[^a-zA-Z0-9%\.,\-\/#@\:\?]/',
    'int'       => '/[^0-9]/',
    'number'    => '/[^0-9]/',
    'float'     => '/[^0-9\.]/',
    'text'      => '/[^a-zA-Z0-9\-_\.\, ]/',
    'text-only' => '/[^a-zA-Z]/',
    'text-safe' => '/[^a-zA-Z0-9\-_]/',
    'date'      => '/[^0-9\/]/',
    'datetime'  => '/[^0-9\/\: ]/',
    'time'      => '/[^0-9\:]/',
    'email'     => '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i'
);

function encriptar($pw)
{
    return md5(sha1(secret_key.$pw));
}

function crear_token($largo, $letras = true, $mayus = true, $numeros = true, $simbolos = false)
{
    $abc = '';
    $token = '';

    if ($letras)
    {
        $abc = 'abcdefghijklmnopqrstuvwxyz';
    }

    if ($mayus)
    {
        $abc .= strtoupper($abc);
    }

    if ($numeros)
    {
        $abc .= '1234567890';
    }

    for ($i = 0;$i < $largo;$i++)
    {
        $token .= substr($abc, rand(0, strlen($abc) - 1), 1);
    }

    return $token;
}

function obtener_ip()
{
    return $_SERVER['REMOTE_ADDR'];
}

function obtener_coordenadas($link)
{
    preg_match_all("/https?\:\/\/www\.google\.com\.ar\/maps\/place\/([,a-zA-Z0-9\%\+\-]+)\/@?([\-\.0-9]+),@?([\-\.0-9]+),([a-zA-Z0-9\.\,]+)\/data=([!a-zA-Z0-9:]+)/i", $link, $elementos, PREG_SET_ORDER);

    return array(
        'link'      => $elementos[0][0],
        'direccion' => $elementos[1][0],
        'x'         => $elementos[2][0],
        'y'         => $elementos[3][0],
        'zoom'      => $elementos[4][0],
        'street'    => $elementos[5][0]
    );
}

function parse($type, $var)
{
    global $regex;

    return preg_replace($regex[$type], '', $var);
}

function test($var, $type)
{
    global $regex;

    if ($type == 'json')
    {
        $try = json_decode($var);
        return (!$try === false);
    }

    $mods = '';
    return (preg_match($regex[$type].$mods, $var));
}

function agregar_ceros($str, $cantidad = 4)
{
    while (strlen($str) < $cantidad)
    {
        $str = '0'.$str;
    }

    return $str;
}

function json($data)
{
    if (!headers_sent())
    {
        header("Content-Type: application/json");
    }

    die(json_encode($data));
}

function is_assoc($array)
{
    return count(array_filter(array_keys($array), 'is_string')) > 0;
}

function extension($nombre)
{
    $partes = explode(".", $nombre);
    $extension = strtolower($partes[count($partes) - 1]);

    return $extension;
}
?>
