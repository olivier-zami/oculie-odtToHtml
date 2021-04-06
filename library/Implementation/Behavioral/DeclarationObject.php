<?php
namespace Oculie;

class DeclarationObject
{
	private $className;
	private $constructorParameters = array();
	private $call = array();

	public function __construct($className, $parameters=array())
	{
		$this->className = $className;
		$this->constructorParameters = array();
	}

	public function __call($methodName, $parameters)
	{
		$this->call[] = array($methodName, $parameters);
		return $this;
	}

	public function __destruct()
	{
		$class = new \ReflectionClass($this->className);
		$instance = $class->newInstanceArgs($this->constructorParameters);
		foreach($this->call as $idx => $call)
		{
			call_user_func_array(array($instance, $call[0]), $call[1]);
		}
		return $instance;
	}
}
?>
