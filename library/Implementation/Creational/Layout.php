<?php
namespace Oculie\Core\Repository;

class Layout extends \Oculie\Core\Repository
{
	private static $handlerInterface;
	private static $instance;
	
	protected static function getInstance()
	{
		return self::$instance;
	}
	
	public static function get($docFileName)
	{
		$templateSrc = "C:\\Users\\ozami\\Projects\\dashboard\\app\\resources\\xhtml\\layout\\".$docFileName.".xhtml";
		
		if(!isset(self::$handlerInterface))
		{
			self::$handlerInterface = new class extends Layout
			{
				use \Oculie\Core\Implementation\xmlHandlerMethods;
				
				public function __construct()
				{
					$this->xmlHandlerInit();
				}
				
				public function display()
				{
					echo self::getInstance()->getAsXML();
				}
			};
		}
		self::$instance = self::$handlerInterface->loadSource(file_get_contents($templateSrc));
		
		return self::$handlerInterface;
	}
}
?>
