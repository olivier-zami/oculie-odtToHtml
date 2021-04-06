<?php
namespace Oculie\Core\Register;

class MetaObject
{
    public static function register($data)
    {
        //TODO: achever le deplacement de ces methodes de  Oculie vers __CLASS__
        /*
        if(isset(self::$appConfig["CLASS_ALIAS"]))
        {
            $classAlias = NULL;
            if(is_string(self::$appConfig["CLASS_ALIAS"]) && file_exists(self::$appConfig["CLASS_ALIAS"]))
            {
                $classAlias = self::eval_conf(self::$appConfig["CLASS_ALIAS"]);
            }
            elseif(is_array(self::$appConfig["CLASS_ALIAS"]))
            {
                $classAlias = self::$appConfig["CLASS_ALIAS"];
            }
            else throw new Exception("parametre invalide");//TODO: externaliser controle dans le cas ou l'on souhaiterai utiliser un autre format

            foreach($classAlias as $alias => $classInfo) self::register_class($alias, $classInfo);
        }
        */
    }

    public static function register_class($classAlias, $classInfo)
    {
        if(isset(self::$registered_class[$classAlias])) throw new Exception("Alias de Pattern \"".$classAlias."\" deja reserve.");
        self::$registered_class[$classAlias] = [];
        self::$registered_class[$classAlias]["name"] = $classInfo["name"];
        self::$registered_class[$classAlias]["factory"] = $classInfo["factory"];
    }
}
?>