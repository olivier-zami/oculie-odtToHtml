<?php
namespace Oculie\Core\DataObject\TypeObject;

class RecordCollection
{
    protected $name;

    public function __construct($name=NULL)
    {
        $this->setName($name);
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}