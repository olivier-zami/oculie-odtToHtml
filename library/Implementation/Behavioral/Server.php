<?php
namespace Oculie;

class Server
{
	public static function getRequest()
	{
		//var_dump($_SERVER["argc"]);
		$requestType = NULL;
		
		
		//if($_SERVER["argc"] && !empty($_SERVER["argv"])) $requestType = Definition\DTO\CommandProcessor::Pattern;//TODO: controller l'existence de l'objet et jeter une exception sinon
		if($_SERVER["argc"] && !empty($_SERVER["argv"])) $requestType = \Dashboard\Definition\DTO\ClearCacheCommand::class; //test creation commande clear-cache
		
		
		$request = new $requestType();
		$argv = $_SERVER["argv"];//TODO le premier element aka nom de l'interpreteur de commande permet d'appeler ... l'interpreteur de commande dans notre cas bin/console
		$commandInterpreterName = array_shift($argv);
		$request->_interpreterName = $commandInterpreterName;
		
		/***********interpreter debut**************/
		//Meme $argv[0] est nromalement le nom du sujet ex bin/console magento2 clear-cache
		$request->_className = $argv[0];//TODO: a mettre dans les metavaleur si le type specifique n'existe pas.
		$request->rawValue = implode(" ", $argv);//TODO le premier element aka nom de l'interpreteur de commande permet d'appeler ... l'interpreteur de commande dans notre cas bin/console
		
		
		$commandNameMapping = [ //TODO: a extraire de quelquepart
			"clear-cache" => "Dashboard\\Controller\\CommandProcessor\\ClearCache" //?Dashboard\Controller\CommandProcessor\Magento2\ClearCache
		];
		//if(isset($commandNameMapping[$argv[0]]) && class_exists($commandNameMapping[$argv[0]])) echo "\nTentative d'instanciation de la commande ".$commandNameMapping[$argv[0]]."\n\n\n";
		/***********interpreter fin**************/
		return $request;
		//Memo: permettre de faire passer un bout de code de var/code (generated) a app afin de le conserver si on y fait des modifs -->generated = priorite basse
	}
}
?>