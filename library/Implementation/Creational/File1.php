<?php
namespace Oculie\Core\Creational\Builder;

class File1
{
	const instance = \Oculie\Core\DataObject\File::class;//TODO: definir le type d'instance créé dans un fichier config

    private static $builder;
	private static $instance;
	
	public static function create($resource=NULL)
	{
		//if(!is_string($resource)) throw new \Exception("Le Builder ".__CLASS__." prend un nom fichier en parametre ".get_class($resource)."");
		$resourceType = self::instance;
		if(!isset(self::$builder)) {$builder = __CLASS__; self::$builder = new $builder();}
		//if(isset($resource) && !file_exists($resource)) throw new \Exception("Le fichier \"".$resource."\" n'existe pas");
		self::$instance = new $resourceType();
		//if(!empty($resource)) $this->getAccessor()->setContent(file_get_contents($resource));
		return self::$builder;
	}

	public function setContent($content)
    {
        self::$instance->setContent($content);
        return self::$builder;
    }

    /*
	public function getAccessor()//or get Model -> le but est de ne pas avoir a réécrire l'Behavior du Model dans le builder
	{
		return \Oculie::data($this->instance);
	}
    */
	
	public function getInstance()
	{
	    return self::$instance;
	}
}
?>