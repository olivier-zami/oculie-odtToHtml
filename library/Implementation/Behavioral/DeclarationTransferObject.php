<?php
namespace Oculie;

class DeclarationTransferObjet
{
	private $className;
	private $constructorParameters = array();
	private $call = array();

	public function __construct($className, $parameters=array())
	{
		$this->className = $className;
		$this->constructorParameters = array();
	}

	public function __set($varName, $value)
	{
		$this->call[] = array($methodName, $parameters);
		return $this;
	}

	public function __get($varName)
	{
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
