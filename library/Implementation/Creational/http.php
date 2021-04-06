<?php
namespace Oculie\Factory\Request;

class Http
{
	public static function create()
	{
		$httpRequest = new \Oculie\DTO\Request\Http();
		$httpRequest->url = $_SERVER["REDIRECT_URL"];
		//echo"<pre>";var_dump($_SERVER);echo"</pre>";
		return $httpRequest;
	}
}
?>