<?php
namespace Oculie\Core\Builder;

class Query
{
    private $field = [];
    private $table;
    private $condition;

    public function getInstance()
    {
        $instance = new \Oculie\Core\DataObject\Sql\Select();
        $instance->setFields(empty($this->field)?["*"]:$this->field);
        $instance->setTable($this->table);
        return $instance;
    }

    public function select()
    {
        $arg = func_get_args();
        if(is_string($arg[0]) && trim($arg[0])=="*")$this->field="*";
        elseif(count($arg)==1 && is_array($arg[0])) foreach($arg[0] as $fName) $this->setField($fName);
        elseif(count($arg)>1) foreach($arg as $fName) $this->setField($fName);
        elseif(count($arg)==1 && is_string($arg[0]))
        {
            $arg = explode(",", $arg[0]);
            foreach($arg as $fName) $this->setField($fName);
        }
        else throw new \Exception("parametrage incorrect");
        return $this;
    }

    public function setField($alias, $fName=NULL)
    {
        if(!isset($fName))
        {
            if(strstr($alias, "as"))
            {
                $tmp = explode("as", strtolower($alias));
                $alias = trim($tmp[1]);
                $fName = trim($tmp[0]);
            }
            elseif(strstr($alias, " "))
            {
                $tmp = explode(" ", strtolower($alias));
                $alias = trim($tmp[1]);
                $fName = trim($tmp[0]);
            }
            else
            {
                $fName = $alias;
            }
        }
        $this->field[$alias] = $fName;
        return $this;
    }

    public function from($tName)
    {
        $this->table = $tName;
        return $this;
    }

    public function where()
    {
        return $this;
    }
}