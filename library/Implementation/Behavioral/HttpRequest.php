<?php
/**
 * Created by PhpStorm.
 * User: ozami
 * Date: 13/02/2020
 * Time: 17:15
 */

namespace Oculie\Core\Processor;


class HttpRequest
{
    private $controller = [];
    
    public function execute($request)
    {
        echo "<fieldset><legend>Dump in file ".__FILE__." @ line ".__LINE__."</legend><pre>".print_r($request,TRUE)."</pre></fieldset>";
        //TODO: rechercher dans la liste des HttpRequestController/WebpageController/WebServiceController + fichier conf Pattern::method,path,parameter) la methode a executer
        foreach($this->controller as $controller)
        {
            /* TODO: a implementer
            $controller->listen($event);
            if($controller->isTrigger())
            {
                $controller->execute($request);//$request contient le path + les var query aaa=1&bbb=2&... TODO: mettre toute les variables dans parameters
                break;
            }
            */
        }
    }

    public function addController($controller)
    {
        //TODO: verifier les doublons de controller + evenement identique ou pouvant etre declanhe en meme temps
        $this->controller[] = $controller;
    }
}