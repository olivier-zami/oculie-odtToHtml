<?php
namespace Oculie\Core\Data\Entity;

class Document
{
    /*
     * Interface
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