<?php
namespace Oculie\Core;

abstract class Extension
{
	const DIR_CONFIG	= NULL;
	const DIR_FIXTURES	= __DIR__ . "/../../../../../fixtures";

	/*
	 * Interface
	 */

	public static function add($classname)
	{
		if(!defined($classname."::NAME"))//TODO: tester subclassof Extension + extension::NAME
		{
			throw new \Exception("Class \"".$classname."\" require a name");
		}

		$extName = $classname::NAME;
		self::$extension[$extName] = new class() {
			public $rootClass;
			public function getClass($classname){return call_user_func_array([$this->rootClass, "getClassOverride"], [$classname]);}
			public function getClasses(){return call_user_func_array([$this->rootClass, "getClassesOverride"], []);}
		};
		self::$extension[$extName]->rootClass = $classname;
	}

	public static function get($idx=NULL): array
	{
		return self::$extension;
	}

	public static function getResourceLocation($filename)
	{
		$calledClass = get_called_class();
		if(!isset(self::$extension[$calledClass])) self::initExtension($calledClass);
		if(!isset(self::$resourceLocation[$filename])) throw new \Exception("unknown register \"".$filename."\"");

		$resourceLocation = NULL;
		if(self::$resourceLocation[$filename] == -1)
		{
			$resourceLocation = self::$extension[$calledClass]->getResourcesLocation();
		}
		else $resourceLocation = self::$resourceLocation[$filename];

		return $resourceLocation;
	}

	public static function init($calledClass)
	{
		self::$extension[$calledClass] = new $calledClass();//TODO: check $class extends \Oculie
		foreach(self::$extension[$calledClass]->getResourcesLocation() as $label => $extensionResourceLocation)
		{
			if(isset(self::$resourceLocation[$label]))
			{
				self::$resourceLocation[$label] = -1;
			}
			else self::$resourceLocation[$label] = $extensionResourceLocation;
		}
	}

	/*
	 * Routines & Properties
	 */

	protected static $extension         = [];
}