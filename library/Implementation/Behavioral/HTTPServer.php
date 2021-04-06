<?php
namespace Oculie;

use Oculie\Core\DataObject\Action;
use Oculie\Core\DataObject\Request\Http;

class HTTPServer
{
	public static function readRequest(){}

	public static function getClientRequest()
	{
		$clientRequest = new \Oculie\Core\DataObject\Request\Http();

		$clientRequest->setMethod($_SERVER["REQUEST_METHOD"]);
		$clientRequest->setUri($_SERVER["SCRIPT_URI"]);
		$clientRequest->setQueryData($_GET);//parse_str($_SERVER["QUERY_STRING"], $queryParameters);
		$clientRequest->setFormData($_POST);

		return $clientRequest;
	}

	public static function getRequestedResource(): Core\DataObject\Resource
	{
		$resource = new Core\DataObject\Resource();
		$resource->setLocation($_SERVER["REQUEST_URI"]);
		return $resource;
	}
	
	public static function getRequestStatus()
	{
		return isset($_SERVER["REDIRECT_STATUS"]) ? $_SERVER["REDIRECT_STATUS"] : 200;
	}
	
	public static function getRequestURI($requestURI=NULL)
	{
		if(isset($_SERVER["REDIRECT_STATUS"])) self::$requestStatus = $_SERVER["REDIRECT_STATUS"];
		
		self::$requestURI = isset($requestURI) ? $requestURI : $_SERVER["REQUEST_URI"];
		return self::$requestURI;
	}
	
	public static function setRequestStatus($code)
	{
		self::$requestStatus = $code;
	}

	/*
	 * Routines & Properties
	 */

	protected static $requestStatus;
	protected static $requestURI;
}
?>
