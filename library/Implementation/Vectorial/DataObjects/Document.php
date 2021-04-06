<?php
namespace Oculie\Core\DataObject;

class Document
{
    /*
     * Behavior
     */

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    /*
     * Routines & Procedures
     */

    protected $content;
}