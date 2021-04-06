<?php
namespace Oculie\Core\DataObject\TypeObject;

class Date
{
    protected $format;

    public function __construct($format=NULL)
    {
        $this->setFormat($format);
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }

    public function getFormat()
    {
        return $this->format;
    }
}