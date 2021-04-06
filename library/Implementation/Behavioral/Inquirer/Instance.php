<?php
namespace Oculie\Core\Inquirer;

class Instance
{
    /*
     * Public Static Methods
     */

    public static function inquire($object): Instance
    {
        if(!is_object($object)&&!is_string($object)&&!class_exists($object))throw new \Exception(__METHOD__." take an object or a valid Pattern name as parameter");

        if(is_object($object))
        {
            self::$className = get_class($object);
            self::$object = $object;
        }
        else
        {
            self::$className = $object;
            self::$object = new $object();
        }

        if(!isset(self::$instance)) self::$instance = new \Oculie\Core\Inquirer\Instance();
        return self::$instance;
    }

    /*
     * Public Instance Methods
     */

    public function isActionObject(): bool
    {
        return (bool)(self::$className==\Oculie\Core\DataObject\Action::class || is_subclass_of(self::$className, \Oculie\Core\DataObject\Action::class));
    }

    public function isEventObject(): bool
    {
        return (bool)(self::$className==\Oculie\Core\DataObject\Event::class || is_subclass_of(self::$className, \Oculie\Core\DataObject\Event::class));
    }

    public function isExceptionObject(): bool
    {
        return (bool)(self::$className==\Exception::class || is_subclass_of(self::$className, \Exception::class));
    }

    /*
     * Methods & properties
     */

    protected function __construct(){}

    protected static $instance;
    protected static $className;
    protected static $object;
}