<?php
/**
 * Created by PhpStorm.
 * User: ozami
 * Date: 13/02/2020
 * Time: 16:17
 */
namespace Oculie\Core\Builder;

class HttpRequest
{
    public static function create()
    {
        return new class()
        {
            private $instance;

            public function __construct()
            {
                $this->instance = new \Oculie\Dto\HttpRequest();
                $this->instance->setPath($_SERVER["SCRIPT_URL"]);

                $parameters = [];
                $queryElement = explode("&", $_SERVER["QUERY_STRING"]);
                foreach($queryElement as $element)
                {
                    $tmp = explode("=", $element);
                    if(count($tmp)>1)$parameters[$tmp[0]] = $tmp[1];
                }

                $this->instance->setParameters($parameters);
            }

            public function fromQuery()//TODO: surcharger la requete ici
            {
                return $this;
            }

            public function getInstance()
            {
                return $this->instance;
            }
        };
    }
}