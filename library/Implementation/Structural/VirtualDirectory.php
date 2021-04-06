<?php
namespace Oculie\Core\Creational\Builder\Iterator;

class VirtualDirectory
{
    const instance = \Oculie\Core\Structural\Iterator\VirtualDirectory1::class;//TODO: definir le type d'instance créé dans un fichier config

    private static $builder;
    private static $instance;
    private static $resourceLocation;
    private static $resourceLocator;

    public static function create($label=NULL)
    {
        if(!isset(self::$builder)) {$builder = __CLASS__; self::$builder = new $builder();}
        self::$resourceLocation = [];
        return self::$builder;
    }

    public function addResourceLocation($url)
    {
        if(is_string($url))
        {
            self::$resourceLocation[$url];
        }
        elseif(is_array($url))
        {
            foreach($url as $urlValue)//TODO: gerer les key comme des chemins relatif qui completeront le chemin de base ($label ou contexte)
            {
                self::$resourceLocation[] = $urlValue;
            }
        }
        else throw new \Exception("Core Error ...");
        return self::$builder;
    }

    public function addResourceLocator($resourceLocator)
    {
        self::$resourceLocator[$resourceLocator];
        return self::$builder;
    }

    public function getInstance()
    {
        $resourceType = self::instance;
        self::$instance = new $resourceType(["location"=>self::$resourceLocation, "locator"=>self::$resourceLocator]);
        return self::$instance;
    }
}