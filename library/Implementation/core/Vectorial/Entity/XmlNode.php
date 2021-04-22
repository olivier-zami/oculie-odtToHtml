<?php
namespace Oculie\Core\Data\Entity;

class XmlNode
{
	/*
	 * Interface
	 */

	use \Oculie\Core\entityPropertiesSettingsMethods;

	/*
	 * Properties & Routines
	 */

	protected $parentName;
	protected $attribute;
	protected $type;
	protected $name;
	protected $value;
	protected $depth;
	protected $path;
}