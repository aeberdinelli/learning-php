<?php
if (!defined('included'))
{
    die;
}

class Router
{
    private $url;
    private $route;
    private $partes;

    private $handlers = array();

    public function __construct($nombre, $ubicacion = '')
    {
        parent::__construct($nombre, $ubicacion);

        $this->route = (isset($_GET['route'])) ? parse('url', $_GET['route']) : '/';
        $this->partes = explode("/", $this->route);
    }

    public function set($url, $handler)
    {
        $this->handlers[$url] = $handler;
    }
}
?>
