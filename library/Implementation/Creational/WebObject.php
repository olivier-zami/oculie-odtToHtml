<?php
namespace Oculie\Core\Repository;

class WebObject extends \Oculie\Core\Repository
{
	private static $handlerInterface;
	private static $instance;
	
	protected static function getInstance()
	{
		return self::$instance;
	}
	
	public static function get($docFileName)
	{
		$templateSrc = "C:\\Users\\ozami\\Projects\\dashboard\\app\\resources\\xhtml\\webobject\\".$docFileName.".xhtml";
		
		self::$instance = json_decode(json_encode([
			"currentResourceName"	=> $docFileName,
			"webObjectDirectory"	=> "C:\\Users\\ozami\\Projects\\dashboard\\app\\resources\\xhtml\\webobject\\"
			]));
		
		if(!isset(self::$handlerInterface))
		{
			self::$handlerInterface = new class extends WebObject
			{
				use \Oculie\Core\Implementation\xmlHandlerMethods;
				
				public function __construct()
				{
					$this->xmlHandlerInit();
				}
				
				public function display()
				{
					echo self::getInstance()->getSource();
				}
				
				public function newInstance()
				{
					//TODO: linker en memoire pour eviter les doublons
					$sourceFile = NULL;
					$core = self::getInstance();
					if(is_string($core->webObjectDirectory) && file_exists($core->webObjectDirectory.$core->currentResourceName.".xhtml"))
					{
						$sourceFile = $core->webObjectDirectory.$core->currentResourceName.".xhtml";
					}
					else throw new \Exception("nom de fichier ".$core->webObjectDirectory.$core->currentResourceName.".xhtml"." introuvable");
					$webObject = new \Oculie\Core\Constructor\WebObject();
					$webObject->loadSource(file_get_contents($sourceFile));
					return $webObject;
				}
			};
		}
		self::$handlerInterface->loadSource(file_get_contents($templateSrc));
		
		return self::$handlerInterface;
	}
}
?>
