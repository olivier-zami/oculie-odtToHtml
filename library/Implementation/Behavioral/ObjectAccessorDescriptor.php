<?php
namespace Oculie;

class ObjectAccessorDescriptor
{
	const CALL 	= 1;
	const GET 	= 2;

	public $instance;
	public $methodType;
	public $methodName;
	public $parameters;

	public function __construct($className, $methodType, $methodName, $parameters=array())
	{
		$this->instance = $className;
		$this->methodType = $methodType;
		$this->methodName = $methodName;
		$this->parameters = $parameters;
	}
}
?>
