<?php
namespace Oculie\Core;

trait queryBuilderMethods
{
    public function select()
    {
        if(!property_exists($this, "queryBuilder"))throw new \Exception("La classe \"".get_class($this)."\" requiert un attribut \"queryBuilder\" pour le trait ".__TRAIT__."");
        if(!isset($this->queryBuilder)) $this->queryBuilder = new \Oculie\Core\Builder\Query();
        call_user_func_array([$this->queryBuilder, "select"], func_get_args());
        return $this;
    }

    public function from($tableName)
    {
        if(!property_exists($this, "queryBuilder"))throw new \Exception("La classe \"".get_class($this)."\" requiert un attribut \"queryBuilder\" pour le trait ".__TRAIT__."");
        if(!isset($this->queryBuilder)) $this->queryBuilder = new \Oculie\Core\Builder\Query();
        call_user_func_array([$this->queryBuilder, "from"], func_get_args());
        return $this;
    }

    public function where()
    {
        if(!property_exists($this, "queryBuilder"))throw new \Exception("La classe \"".get_class($this)."\" requiert un attribut \"queryBuilder\" pour le trait ".__TRAIT__."");
        if(!isset($this->queryBuilder)) $this->queryBuilder = new \Oculie\Core\Builder\Query();
        call_user_func_array([$this->queryBuilder, "where"], func_get_args());
        return $this;
    }
}