<?php
namespace Oculie\Core\Processor;

class Command
{
	protected static $processor = "Oculie\\execute_command";
	
	public static function execute($command)
	{
		if(!is_subclass_of($command, \Oculie\Definition\DTO\Command::class))throw new \Exception("La methode ".__METHOD__." prend des objects de type \"".\Oculie\Definition\DTO\Command::class."\" comme parametre.");
		call_user_func_array(self::$processor, [$command]);
	}
}
?>
