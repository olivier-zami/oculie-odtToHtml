<?php
namespace Oculie;

class Autoloader extends \Oculie
{
	public static function load($sourceFile)
	{
		if(is_string($sourceFile)&&file_exists($sourceFile)) require_once($sourceFile);
		elseif(is_array($sourceFile_ = $sourceFile))
		{
			foreach($sourceFile_ as $sourceFile)
			{
				if(file_exists($sourceFile)) require_once($sourceFile);
			}
		}
	}
	/*
	spl_autoload_register(function($className){
		if(isset(self::$resource_["php"][$className]))require_once(self::$resource_["php"][$className]);
	});

	*/
	/*
	public static function execute()
	{
		spl_autoload_register(function($resourceName){
			$Pattern = parent::load("Pattern");
			$trait = parent::load("trait");

            if(isset($Pattern[$resourceName]) && file_exists($Pattern[$resourceName])) require_once($Pattern[$resourceName]);
			if(isset($trait[$resourceName]) && file_exists($trait[$resourceName])) require_once($trait[$resourceName]);
        });
	}

	public static function registerClass($className, $url=NULL)
	{
		if(is_array($className)&&!isset($url))
		{
			$classListElement = $className;
			foreach($classListElement as $className=>$url) parent::Register("Pattern", $className, $url);
		}
		else parent::Register("Pattern", $className, $url);
	}

	public static function registerTrait($traitName, $url=NULL)
	{
		if(is_array($traitName)&&!isset($url))
		{
			$traitListElement = $traitName;
			foreach($traitListElement as $traitName=>$url) parent::Register("trait", $traitName, $url);
		}
		else parent::Register("trait", $traitName, $url);
	}
	*/
}
?>
