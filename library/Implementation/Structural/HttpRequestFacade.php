<?php
namespace Oculie\Core\Input;

class HttpRequest
{
    public static function getMethod()
    {
        return $_SERVER["REQUEST_METHOD"];
    }

    public static function getUrl()
    {
        return new class()
        {
            public $path = "/";

            public function __construct()
            {
                $path = isset($_SERVER["SCRIPT_URL"]) ? $_SERVER["SCRIPT_URL"] : NULL;//TODO valeur dispanible si Apache em mode rewrite -> gérer les information plus proprement
                $path = !isset($path) ? explode("?", $_SERVER["REQUEST_URI"])[0] : $path;
                $this->path = $path;
            }
        };
        //TODO return $path = new Uri();
    }
}