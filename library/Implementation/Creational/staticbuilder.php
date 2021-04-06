<?php
namespace Oculie\Core;

class StaticBuilder
{	
	public static function getInstanceOf($resource)
	{
		//if(property_exists(get_called_class(), "builder")) echo "<br/>C'est ok pour ".get_called_class();
		self::setInstanceIfNotExist(static::$builderInstance, static::$builder);//static::$builderInstance = new static::$builder();
		static::$builderInstance->create($resource);
		return static::$builderInstance->getInstance();
	}
	
	protected static function setInstanceIfNotExist(&$instance, $builder)
	{
		if(!class_exists($builder)) throw new \Exception("Un monteur static requière un monteur concret pour fonctionner");
		if(!isset($instance)) $instance = new $builder();
		$instance->create();
	}
}
?>