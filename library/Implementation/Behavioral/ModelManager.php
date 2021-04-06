<?php
namespace Oculie;

class ModelManager extends \Oculie
{
	protected static $model = array();

	public static function getModel($modelName)
	{
		return parent::getMockInterface($modelName);
	}

	public static function newModel($className)
	{
		$modelBuilder = new class(){use \DeclarativeBuilderObjectFluentInterfaceTrait;};
		return $modelBuilder;
	}

	public static function registerModel($modelName, $class)
	{
		return parent::declare($modelName, $class);
	}
}
?>
