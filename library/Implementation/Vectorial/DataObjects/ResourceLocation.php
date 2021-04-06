<?php
namespace Oculie\Core\DataObject;

class ResourceLocation
{
    protected $file;

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }
}