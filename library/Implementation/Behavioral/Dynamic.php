<?php

namespace Oculie\Core\DataObject;

class Dynamic extends \Oculie\Core\DataObject
{
    protected $content = [];

    public function __call($method, $parameter)
    {
        $return = NULL;
        if(substr($method, 0, 3)=="get")
        {
            if(!isset($this->content[$attr = lcfirst(substr($method, 3))]))
            {
                throw new \Exception("Le container ".get_class($this)." ne contient pas d'attribut \"".$attr."\"");
            }
            $return = $this->content[$attr];
        }
        elseif(substr($method, 0, 3)=="set")
        {
            //TODO: cette methode de doit avoir q'un parametre
            $this->content[lcfirst(substr($method, 3))] = $parameter[0];
        }
        return $return;
    }
}