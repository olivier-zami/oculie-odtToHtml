<?php
namespace Oculie\Core;

trait entityPropertiesSettingsMethods
{
	public function __set($name, $value)
	{
		if(property_exists($this, $name)) $this->{$name} = $value;
	}

	public function __get($name)
	{
		return property_exists($this, $name) ? $this->{$name} : NULL;
	}
}