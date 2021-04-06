<?php
namespace Oculie\Core\Builder;

class Document
{
    /*
     * Behavior
     */

    public static function create(): Document
    {
        if(!isset(self::$builder))self::$builder = new \Oculie\Core\Builder\Document();
        return self::$builder;
    }

    public function setContent($content): Document
    {
        $this->content = $content;
        return $this;
    }

    public function setUri($uri): Document//TODO: normer le nom du fichier ou utiliser un objet fichier
    {
        $this->content = file_get_contents($uri);//TODO: tester l'existence de DTD
        return $this;
    }

    public function getInstance(): \Oculie\Core\DataObject\Document
    {
        $instance = new \Oculie\Core\DataObject\Document();
        $instance->setContent($this->content);
        $this->content = NULL;
        return $instance;
    }

    /*
    * Routines & Properties
    */

    protected static $builder;

    private $content;
}