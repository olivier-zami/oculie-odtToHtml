<?php
namespace Oculie\Core\DataObject;

class File extends \Oculie\Core\Abstraction\Model\File
{
    protected $content;

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }
}