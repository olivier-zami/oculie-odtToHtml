<?php
namespace Oculie\Core\Builder;

class Resource extends \Oculie\Core\Abstraction\Pattern\Builder
{
    /*
     * Behavior
     */

    public static function create(): Resource
    {
        if(!isset(self::$builder))self::$builder = new \Oculie\Core\Builder\Resource();
        return self::$builder;
    }

    public function __construct()
    {
        $this->id       = NULL;
        $this->type     = NULL;
        $this->location = NULL;
        $this->content  = NULL;
    }

    public function setType($type): Resource
    {
        $this->type = $type;
        return $this;
    }

    public function setLocation($location): Resource
    {
        $this->location = $location;
        return $this;
    }

    public function setContent($content): Resource
    {
        $this->content = $content;
        return $this;
    }

    public function getInstance(): \Oculie\Core\DataObject\Resource
    {
        $instance = new \Oculie\Core\DataObject\Resource();
        $instance->setType($this->type);
        $instance->setLocation($this->location);
        $instance->setContent($this->content);
        $this->type= NULL;
        $this->location = NULL;
        $this->content = NULL;
        return $instance;
    }

    /*
     * Routines & Properties
     */

    protected static $builder;

    private $id       = NULL;
    private $type     = NULL;
    private $location = NULL;
    private $content  = NULL;
}