<?php
namespace Oculie\Core;

class Application extends \Oculie//TODO: creer une application comme une instance qui apelle un ou  plusieurs processor
{
    /*
     * Behavior
     */

    /*
     * Variables & Procedures
     */

    protected static $modelConfiguration        = NULL;//TODO: affecter la valeur [] si pas de Model

    protected static function setModelConfiguration($modelConfiguration)
    {
        $oldConfiguration = self::getModelConfiguration();
        if(!isset($oldConfiguration)) $oldConfiguration = [];

        self::$modelConfiguration = array_merge_recursive($oldConfiguration, $modelConfiguration);
    }

    protected static function getModelConfiguration()
    {
        return self::$modelConfiguration;
    }
}

