<?php
namespace Oculie\Core\Abstraction\Pattern;

abstract class Inquirer
{
    /*
     * Behavior
     */

    public static function inquire($object)
    {
        static::$object = $object;
        if(!isset(self::$inquirer))
        {
            $called_class = get_called_class();
            static::$inquirer = new $called_class();
        }
        return static::$inquirer;
    }

    /*
     * Routines & Properties
     */

    protected static $inquirer;
    protected static $object;
}