<?php
namespace Oculie\Core\Handler;

class Event extends \Oculie
{
	private static $currentEventName 	= NULL;
	private static $event				= [];
	
	public static function addEventParameters($eventName, $parameters=[])
	{
		self::$currentEventName = $eventName;
		self::$event[$eventName]["parameters"] = array_merge(self::$event[$eventName]["parameters"], $parameters);
	}
	
	public static function create($eventName, $trigger=NULL, $parameters=[])
	{
		self::$currentEventName = $eventName;
		self::$event[$eventName] = ["trigger"=>$trigger, "parameters"=>$parameters];
		self::getConfiguration();
	}
	
	public static function getCurrentEventName()
	{
		return self::$currentEventName;
	}
	
	public static function setEventTrigger($eventName, $callable)
	{
		self::$currentEventName = $eventName;
		self::$event[$eventName]["trigger"] = $callable;
	}
	
	public static function start($action=NULL)
	{
		foreach(self::$event as $eventName => $event)
		{
			if(!call_user_func_array($event["trigger"], $event["parameters"]))continue;
			parent::start($eventName);
		}
	}
}
?>