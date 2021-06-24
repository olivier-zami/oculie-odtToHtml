<?php
namespace Oculie\Core\Data\Entity;

class XmlNode
{
	/*
	 * Interface
	 */

	use \Oculie\Core\entityPropertiesSettingsMethods;

	public static function __set_state($property)
	{
		$state = new class(){};
		$state->parentName = $property["parentName"];
		$state->tagType = $property["tagType"];
		$state->attribute = $property["attribute"];
		$state->type = $property["type"];
		$state->name = $property["name"];
		$state->value = $property["value"];
		$state->depth = $property["depth"];
		$state->path = $property["path"];
		return $state;

	}

	/*
	 * Properties & Routines
	 */

	protected $parentName;
	protected $tagType;
	protected $attribute;
	protected $type;
	protected $name;
	protected $value;
	protected $depth;
	protected $path;
}