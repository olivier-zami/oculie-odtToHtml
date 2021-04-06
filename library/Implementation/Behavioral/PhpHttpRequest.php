<?php
namespace Oculie\Interpreter;

Use Oculie\Utils\Debug;

class PhpHttpRequest
{
    protected static $requestEntity;

    public static function parse($request)
    {
        //request doit avoir la propriété uri
        self::$requestEntity = $request;

        /*
        Debug::dump([
            "request"       => $request,
        ], FALSE);
        */

        return new class() extends PhpHttpRequest{ //TODO: utiliser une Behavior de traduction
            public function getRequest()
            {
                $request = new \Oculie\Entity\HttpRequest();
                $request->action = "read";//TODO: gerer les Action write et execute
                $request->resourceLocation = self::$requestEntity->uri;
                $request->resource = new \Oculie\Entity\Resource();
                $request->resource->name = "";
                return $request;
            }
        };
    }
}