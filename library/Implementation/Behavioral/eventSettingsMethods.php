<?php
namespace Oculie\Action\Behavior;

trait eventSettingsMethods
{
	public function create($name)
	{
		\Oculie\Core\Handler\Event::create($name);
		return $this;
	}
	
	public function checkingCondition($callable)
	{
		$eventName = \Oculie\Core\Handler\Event::getCurrentEventName();
		\Oculie\Core\Handler\Event::setEventTrigger($eventName, $callable);
		return $this;
	}
	
	public function withParameters($parameters=[])
	{
		$eventName = \Oculie\Core\Handler\Event::getCurrentEventName();
		\Oculie\Core\Handler\Event::addEventParameters($eventName, $parameters);
		return $this;
	}
}
?>