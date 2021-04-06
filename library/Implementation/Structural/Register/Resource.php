<?php
namespace Oculie\Core\Register;

class Resource
{
    protected $directory;

    public function getDirectories($alias=NULL)
    {
        if(!isset($alias)) throw new \Exception("Alias must be define in method \"".__METHOD__."\"");
        if(!isset($this->directory[$alias])) throw new \Exception("Alias \"".$alias."\" referes to no directories");
        return $this->directory[$alias];
    }
}