<?php
Trait DeclarativeBuilderObjectFluentInterfaceTrait
{
	private $declarativeBuilder = array("name"=>NULL, "call"=>array());

	public function __call($methodName, $parameters)
	{
		$this->declarativeBuilder["call"][] = array($methodName, $parameters);
		return $this;
	}

	public function getBuildedInstance()
	{
		$instance = new $this->declarativeBuilder["name"]();
		foreach($this->declarativeBuilder["call"] as $idx => $call)
		{
			call_user_func_array(array($instance, $call[0]), $call[1]);
		}
		return $instance;
	}

	public function setBuildedClassName($className)
	{
		$this->declarativeBuilder["name"] = $className;
	}
}
?>
