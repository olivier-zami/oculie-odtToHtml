<?php
namespace Oculie\Reader;

class HttpRequest
{
    protected $requestUri;

    public static function read($input=NULL)
    {
        $requestUri = $_SERVER["REQUEST_URI"];
        if(isset($_SERVER["REDIRECT_STATUS"]) && $_SERVER["REDIRECT_STATUS"]="404") {}

        $request = new class(){
            public function getPhpHttpRequest()
            {
                $request = new \Oculie\Entity\HttpRequest();
                $request->uri = $_SERVER["REQUEST_URI"];
                return $request;
            }
        };
        return $request;
    }
}