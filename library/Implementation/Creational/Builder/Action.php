<?php
namespace Oculie\Core\Builder;

class Action extends \Oculie\Core\Abstraction\Pattern\Builder
{
    /*
     * Behavior
     */

    public static function create(): Action
    {
        if(!isset(self::$builder))self::$builder = new \Oculie\Core\Builder\Action();
        return self::$builder;
    }

    public function __construct()
    {
        $this->method = null;
        $this->parameters = NULL;
    }

    public function setMethod($method): Action
    {
        $this->method = $method;
        return $this;
    }

    public function setParameters($parameters=[]): Action
    {
        $this->parameters = func_get_args();
        return $this;
    }

    public function getInstance(): \Oculie\Core\DataObject\Action
    {
        $instance = new \Oculie\Core\DataObject\Action();
        $instance->setMethod($this->method);
        call_user_func_array([$instance, "setParameters"], $this->parameters);
        $this->method= NULL;
        $this->parameters = [];
        return $instance;
    }

    /*
     * Routines & Properties
     */

    protected static $builder;

    private $method;
    private $parameters = [];
}