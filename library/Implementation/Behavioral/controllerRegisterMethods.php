<?php
namespace Oculie;

trait controllerRegisterMethods
{
	public function execute($executable)
	{
		$this->actionName = \Oculie::registerAction($executable);
		\Oculie::getConfiguration($this->context)["CONTROLLER"][$this->controlerName]["callback"] = [[$executable]];
		return $this;
	}
	
	public function onContext($context)
	{
		$this->context = $context;
	}
	
	public function onEvent($eventName)
	{
		$this->eventId = $eventName;
		$this->context = isset($this->context) ? $this->context : "NO_CONTEXT";
		$this->registeredType = "Pattern";
		$this->registeredTypeName = "Event";
		$this->registeredId = $eventName;
		if(!isset(\Oculie::getConfiguration($this->context)["CONTROLLER"]))\Oculie::getConfiguration($this->context)["CONTROLLER"]=[];
		$this->controlerName = "ctrl_".count(\Oculie::getConfiguration($this->context)["CONTROLLER"]);
		\Oculie::getConfiguration($this->context)["CONTROLLER"][$this->controlerName] = ["event-name"=>$eventName];
		return $this;
	}
	
	public function withMethod($method)
	{
		\Oculie::getConfiguration($this->context)["CONTROLLER"][$this->controlerName]["callback"][0][1] = $method;
		return $this;
	}
	
	public function withParameters()
	{
		\Oculie::getConfiguration($this->context)["CONTROLLER"][$this->controlerName]["callback"][1] = func_get_args();
		return $this;
	}
}
?>