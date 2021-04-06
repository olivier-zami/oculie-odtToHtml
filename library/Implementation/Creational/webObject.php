<?php
namespace Oculie\Core\Constructor;

class WebObject
{
	use \Oculie\Core\Implementation\xmlHandlerMethods;
	
	public function __construct()
	{
	}
	
	public function getDOM()
	{
		return $this->select("/html/body/*")->getAsDOM();
	}
	
	public function getTemplate()
	{
		return $this->select("/html/body/*")->getAsXML();
	}
}
?>
