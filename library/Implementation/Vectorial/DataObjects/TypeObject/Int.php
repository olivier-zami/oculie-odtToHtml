<?php
namespace Oculie\Core\DataObject\TypeObject;

class Int
{
    protected $size;

    public function __construct($size=NULL)
    {
        $this->setSize($size);
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function getSize()
    {
        return $this->size;
    }
}