<?php
/**
 * Created by PhpStorm.
 * User: ozami
 * Date: 18/02/2020
 * Time: 10:48
 */

namespace Oculie\Core\Builder;


class Event
{
    /*
     * Behavior
     */
    public static function create(): Event
    {
        if(!isset(self::$instance))self::$instance = new \Oculie\Core\Builder\Event();
        return self::$instance;
    }

    public function __construct()
    {
        $this->name = NULL;
        $this->trigger = NULL;
    }

    public function getInstance(): \Oculie\Core\DataObject\Event
    {
        self::$instance = new \Oculie\Core\DataObject\Event();
        self::$instance->setName($this->name);
        $this->name = NULL;
        self::$instance->setTrigger($this->trigger);
        $this->trigger = NULL;
        return self::$instance;
    }

    public function setName($name): Event
    {
        $this->name = $name;
        return $this;
    }

    public function setTrigger($trigger): Event
    {
        $this->trigger = $trigger;
        return $this;
    }

    /*
     * Routines & Properties
     */

    protected static $instance;

    private $name;
    private $trigger;
}