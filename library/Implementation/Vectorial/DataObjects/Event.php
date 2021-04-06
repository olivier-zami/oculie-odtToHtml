<?php
namespace Oculie\Core\DataObject;

class Event
{
    /*
     * Behavior
     */

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setTrigger($trigger)
    {
        $this->trigger = $trigger;
    }

    public function getTrigger()
    {
        return $this->trigger;
    }

    /*
     * Routines & Properties
     */

    private $name;
    private $trigger;
}