<?php
namespace Oculie\Core;
 
class DAO
{
    private static $core;
    
    public static function getCore()
    {
        return self::$core;
    }
    
    public static function setCore($coreInstance)
    {
        self::$core = $coreInstance;
    }
}
?>
