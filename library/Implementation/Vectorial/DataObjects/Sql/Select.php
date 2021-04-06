<?php
namespace Oculie\Core\DataObject\Sql;

class Select
{
    protected $fields = ["*"];
    protected $table;

    public function getFields()
    {
        $fields = [];
        if(isset($this->fields[0]) && $this->fields[0]=="*")$fields=["*"];
        else foreach($this->fields as $alias => $fName)
        {
            $fields[] = ($alias==$fName)? $fName : $fName." ".$alias;
        }
        return $fields;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    public function setTable($table)
    {
        $this->table = $table;
    }
}