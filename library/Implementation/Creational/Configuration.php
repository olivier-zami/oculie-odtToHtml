<?php
namespace Oculie\Core\Builder;

class Configuration
{
	const instance = \Oculie\Core\DataObject\Configuration::class;
	
	private $instance;
	
	public function create($resource)
	{
		//$resource est une chaine de caractere ou une resource de type fichier
		//if(!is_string($resource)) throw new \Exception("Le Builder ".__CLASS__." prend un nom fichier en parametre ".get_class($resource)."");
		$instance = self::instance;
		$this->instance = new $instance();
		$content = is_string($resource) ? $resource : \Oculie::data($resource)->getContent();
		$configDto = self::instance;
		$this->instance = new $configDto();
		$this->generateConfiguration($content);
		return $this;
	}
	
	public function getInstance()
	{
		$instance = $this->instance;
		$this->instance = NULL;
		return $instance;
	}
	
	private function generateConfiguration($xml)
	{
		$doc = new \DomDocument();
		$doc->loadXml($xml);
		$stack[] = ["node"=>$doc->firstChild->firstChild, "dto"=>$this->instance];
		while(!empty($stack))
		{
			for($node=reset($stack)["node"]; $node; $node=$node->nextSibling)
			{
				switch($node->nodeType)
				{
					case 1:
						if($node->getAttribute("type")=="Pattern")
						{
							$dtoClass = self::instance;
							$instance = new $dtoClass();
						}
						elseif($node->hasChildNodes() && $node->childNodes->count() > 1) $instance = [];
						else $instance = NULL;
						
						
						if(is_object(reset($stack)["dto"]))
						{
							$stack[0]["dto"]->{$node->nodeName} = $instance;
							$ptr = &$stack[0]["dto"]->{$node->nodeName};
							if($node->hasChildNodes()) $stack[] = ["node"=>$node->firstChild, "dto"=>&$ptr];
						}
						else
						{
							$stack[0]["dto"][$node->nodeName] = $instance;
							$ptr = &$stack[0]["dto"][$node->nodeName];
							$stack[] = ["node"=>$node->firstChild, "dto"=>&$ptr];
						}
						
						break;
						
					case 3:
						if(!empty(trim($node->data)))
						{
							$stack[0]["dto"] = $node->data;
						}
						break;					
					default:
						break;
				}
			}
			array_shift($stack);
		}
	}
}
?>