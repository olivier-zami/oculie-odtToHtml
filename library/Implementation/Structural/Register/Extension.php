<?php
namespace Oculie\Core\Register;

class Extension
{
    protected static $class     = [];
    protected static $object    = [];

    protected static $instance  = [];

    public static function bind($subject)
    {
        $bind = NULL;
        if(is_string($subject) && class_exists($subject))
        {
            $linkClassNamespace = \Oculie\Core\Linker::class;
            $tmp = explode("\\", $subject);
            if(!isset(self::$class[$subject]) && class_exists($linker = $linkClassNamespace."\\".end($tmp)))self::$class[$subject] = new $linker();
            $bind = self::$class[$subject];
        }
        if(!isset($bind))throw new \Exception();
        return $bind;
    }

    public static function create($name, $extensionClass=NULL)
    {
        if(isset($extensionClass))
        {
            self::$instance[$name] = new $extensionClass();
        }
        else self::$instance[$name] = new \Oculie\Core\Extension();
    }

    public static function get($name)
    {
        return self::$instance[$name];
    }

    public static function getAll()
    {
        return self::$instance;
    }
}