<?php

namespace Oculie\Core\DataObject;

class Tabular extends \Oculie\Core\DataObject
{
    protected $content;

    public function setContent($content)
    {
        if(!is_array($content))throw new \Exception(__METHOD__."Ne peut prendre qu'un tableau en parametre");
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }
}