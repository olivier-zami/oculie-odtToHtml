<?php
namespace Oculie;

class RouteManager extends \Oculie
{
	private static $route;

	public static function isURIMatchingRoute($routeName)
	{
		//TODO: verifier la route correctement
		return (bool) ($_SERVER["REQUEST_URI"] == parent::load($routeName));
	}

	public static function getRoute($name)
	{
		return self::$route[$name];
	}

	public static function registerRoute($name, $value)
	{
		self::$route[$name] = $value;
	}
}
?>
