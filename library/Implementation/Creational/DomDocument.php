<?php
namespace Oculie\Core\Constructor;

class DomDocument
{
	use \Oculie\Core\Implementation\xmlHandlerMethods;
	
	public function __construct($source=NULL)
	{
		if(is_object($source))
		{
			
		}
		elseif(is_string($source))
		{
			$this->loadSource($source);
		}
		elseif(!isset($source))
		{
			$source = "<xml></xml>";
			$this->loadSource($source);
		}
		else throw new \Exception("Tentative de creation d'un object \".__CLASS__.\" avec une donnée de type ".gettype($source)."");
	}
}
?>