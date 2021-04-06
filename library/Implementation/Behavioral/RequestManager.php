<?php
namespace Oculie;

class RequestManager extends \Oculie
{
	const PROTOCOL_HTTP = 1;

	protected static $action = array();

	protected static $tmpRequestObject;//TODO: rechercher le requestedObject dans l'objet requete instanciée

	private static $defaultResourceViewer = NULL;
	private static $option = array(
		"ENABLE_DIRECT_RESOURCE_ACCESS"	=> TRUE
	);

	public static function enableDirectResourceAccess($bool)
	{
		self::$option["ENABLE_DIRECT_RESOURCE_ACCESS"] = (bool)$bool;
	}

	public static function getRequest($name)
	{
		return self::$action[$name];
	}

	public static function getMessageObject()
	{
		return self::$tmpRequestObject;
	}

	public static function listen($option=array()/*HTTP_PROTOCOL or else*/)
	{
		//NOTE: ecouter "tout" ou ce qui est filtré par les options

		/*
		$order = self::newOrder();
		$order->request["path"] = $_SERVER["REQUEST_URI"];
		*/
		$requestName = self::getRequestName();
		echo $requestName;

		if($requestName = self::getRequestName())//TODO: creer une table indexé par valeur d'uri afin d'acceler l'algo
		{
			var_dump($requestName);
			echo"<pre>";print_r(self::$action[$requestName]);echo"</pre>";
			/*
			$order = new Pattern()
			{
				//use \DataTransferObjectPropertyInterface;
				use \DeclarativeBuilderObjectFluentInterfaceTrait;
				protected $header = array();
				protected $body = array();
				protected $footer = array();
			};
			$order->header["status"] = 200;

			$order->footer["viewer"] = new Pattern(\Process\Handler\DocumentCss::Pattern)
			{
				use \DeclarativeBuilderObjectFluentInterfaceTrait;
			};
			$order->footer["viewer"]->setBuildedClassName(\Process\Handler\DocumentCss::Pattern);
			$order->footer["viewer"]->handle(file_get_contents(parent::getDirPublic().$_SERVER["REQUEST_URI"]));


			$fileInfo = \pathinfo(parent::getDirPublic().$_SERVER["REQUEST_URI"]);//TODO: faire une methode permettant de recuperer des information fichiers
			switch(strtolower($fileInfo["extension"]))
			{
				case "css":
					$order->header["content-type"] = "text/css";
					break;
			}
			*/
		}
		elseif(self::$option["ENABLE_DIRECT_RESOURCE_ACCESS"])
		{
			$order->process["type"]		= self::PROCESS_TYPE_DIRECT_RESOURCE_ACCESS;
			$order->process["viewer"] 	= self::$defaultResourceViewer;
			$order->receipt["status"] 	= 404;

			if(is_file($file = parent::getDirPublic().$_SERVER["REQUEST_URI"]))
			{
				$order->receipt["status"] 	= 200;
				$order->process["uri"]		= $file;
			}
			elseif(is_dir(parent::getDirPublic().$_SERVER["REQUEST_URI"])&&is_file($file = parent::getDirPublic().$_SERVER["REQUEST_URI"]."index.html"))
			{
				$order->receipt["status"] 	= 200;
				$order->process["uri"]		= $file;
			}
			else throw new \Exception("Ressource introuvable.");
		}
		else
		{
			$idxRequest = NULL;
			foreach(self::$action as $idx => $request)
			{
				if(self::$action[0]->header["URI"]->path == $_SERVER["REQUEST_URI"])
				{
					$idxRequest = $idx;
				}
				//if($request->isMatch())break;
			}
			if(!isset($idxRequest))throw new \Exception("Requete inatendue");
			$order = self::$action[$idxRequest];
			$order->header["status"] = 200;
		}
		parent::push($order);
	}

	public static function getRequestName($uri=NULL)
	{
		if(!isset($uri)) $uri = $_SERVER["REQUEST_URI"];
		$name = NULL;
		foreach(self::$action as $id => $trigger)
		{
			$path = preg_replace("|\\\$[a-zA-Z_]+|", "([a-zA-Z_]+)", $trigger->event->resource);//TODO: remplacer les valeur dans l'amorcage
			$path = "|^".$path."\$|";
			$match = NULL;
			if(preg_match($path, $uri, $match)){$name=$id; break;}
		}
		return $name;
	}

	public static function newMessageObject($message=NULL)
	{
		return $message;
	}
	public static function newRequest() {return parent::newChainedMutatorClass(Entity\Request::class);}

	public static function onRequest($request)//NOTE: $request = RequestManager::newRequest()
	{
		$key = "action_".count(self::$action);
		self::$action[$key] = NULL;
		return self::$action[$key] = parent::newTrigger()
			->onEvent($request);
	}

	public static function setMicroController($name, $request, $message, $receiver)
	{
		self::$action[$name] = parent::newTrigger()
			->onEvent($request)
			->send($message)
			->to($receiver);
	}

	public static function setDefaultResourceViewer($className)
	{
		self::$defaultResourceViewer = $className;
	}
}
?>
