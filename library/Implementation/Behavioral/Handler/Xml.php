<?php
namespace Oculie\Core\Handler;

use DOMDocument;
use DomText;
use DOMXpath;

class Xml extends \Oculie\Core\Handler
{
	/*
	 * Behavior
	 */

	//public function insert(){}

	//public function get(){}

	public function change($form1, $form2)
	{

	}

	public function changeFor($content)
	{
		$this->writeText($content);
	}

	public function changeUsing($method)
	{

	}

	//public function delete()

	/*
	 * Routines & properties
	 */

	//TODO: implement Behavior ?
	private $document;
	private $query;
	private $xpath;
	private $queryTarget;

	const NODE_TYPE_NONE 					= 0;
	const NODE_TYPE_ELEMENT 				= 1;
	const NODE_TYPE_ATTRIBUTE 				= 2;
	const NODE_TYPE_TEXT 					= 3;
	const NODE_TYPE_CDATA 					= 4;
	const NODE_TYPE_ENTITY_REF 				= 5;
	const NODE_TYPE_ENTITY 					= 6;
	const NODE_TYPE_PI 						= 7;
	const NODE_TYPE_COMMENT 				= 8;
	const NODE_TYPE_DOC 					= 9;
	const NODE_TYPE_DOC_TYPE 				= 10;
	const NODE_TYPE_DOC_FRAGMENT 			= 11;
	const NODE_TYPE_NOTATION 				= 12;
	const NODE_TYPE_WHITESPACE 				= 13;
	const NODE_TYPE_SIGNIFICANT_WHITESPACE 	= 14;
	const NODE_TYPE_END_ELEMENT 			= 15;
	const NODE_TYPE_END_ENTITY 				= 16;
	const NODE_TYPE_XML_DECLARATION 		= 17;

	const EXTERNAL_NODE_TYPE_TEXT			= 1;
	const EXTERNAL_NODE_TYPE_TEMPLATE		= 2;

	public function __construct(){}

	//!
	public function setOptions($option){}

	public function setInputEncodage(){}

	//!
	public function handle($data)
	{
		//$this->document = new \DOMDocument("1.0", "iso-8859-1");//TODO: créer methode setEncodage
		$this->document = new \DOMDocument("1.0", "utf-8");//TODO: créer methode setEncodage
		$this->document->preserveWhiteSpace = FALSE;
		$this->document->formatOutput = true;
		$this->document->loadXML($data);
		$this->xpath = new \DOMXpath($this->document);
		$this->query = "";
		$this->queryTarget = NULL;
		return $this;
	}

	public function getSource($asArray = FALSE)//TODO: le parametre as array permet de forcer le retour de la source sous forme de tableau
	{
		if(!isset($this->document))throw new \Exception("Tentative de manipulation sur une ressource non definie");//TODO: d�fini �crit avec un accent aigu n'est pas afficher -> a corriger

		if(empty($this->queryTarget)) $result = $this->document->saveXML();
		else
		{
			$result = array();
			for($i=0; $i<$this->queryTarget->length; $i++)
			{
				$element = $this->queryTarget->item($i);
				switch($element->nodeType)
				{
					case self::NODE_TYPE_ELEMENT:
						$result[] = $this->document->saveXML($element);
						break;
					case self::NODE_TYPE_ATTRIBUTE:
						$result[] = $element->value;
						break;
					case self::NODE_TYPE_TEXT:
					case self::NODE_TYPE_CDATA:
						$result[] = $element->wholeText;
						break;
					default:
						throw new \Exception("<p>Les noeuds du type \"".$this->nodeTypeName($element->nodeType)."\" ne sont pas gerer par le moteur de template</p>");
						break;
				}
			}
		}

		$this->queryTarget = NULL;
		if(is_array($result))
		{
			if(count($result)==1)
			{
				$result = htmlspecialchars_decode(htmlentities($result[0], ENT_NOQUOTES, 'UTF-8'));
			}
		}
		return $result;
	}

	public function getDocument()
	{
		return $this->document;
	}

	///!Operateurs terminaux

	public function appendAfter($data, $offset=0)
	{
		if(!isset($this->document))throw new \Exception("import d'un de donnee dans un document inexistant");
		$importedNode = $this->import($data);
		for($i=0;$i<$this->queryTarget->length;$i++)
		{
			$node = $this->queryTarget->item($i);
			switch(get_class($node))
			{
				case \DOMElement::class:
					if(!isset($node->nextSibling))
					{
						$node->parentNode->appendChild($this->document->importNode($importedNode, TRUE));
					}
					else
					{
						$target = $node;
						do
						{
							$target = $target->nextSibling;
							$offset--;
						}while(isset($target->nextSibling) && $offset>=0);
						$node->parentNode->insertBefore($this->document->importNode($importedNode, true), $target);
					}
					break;
				default:
					throw new \Exception("Les noeuds du type \"".$this->nodeTypeName($node->nodeType)."\" ne sont pas gerer par le moteur de template");
					break;
			}
		}
		return $this;
	}

	public function appendBefore($data){}

	public function delete()
	{
		if(!isset($this->document))throw new \Exception("import d'un de donnee dans un document inexistant");
		for($i=0; $i<$this->queryTarget->length; $i++)
		{
			$element = $this->queryTarget->item($i);
			$element->parentNode->removeChild($element);
		}
		return $this;
	}

	public function getNode()
	{
		if(!isset($this->document))throw new \Exception("import d'un de donnee dans un document inexistant");
		if($this->queryTarget->length>1)throw new \Exception("La requete \"".$this->query."\" retourne plus d'un noeuds");
		return $this->queryTarget->length ? $this->queryTarget->item(0) : NULL;
	}

    protected function getNodes()
    {
        if(!isset($this->document))throw new \Exception("import d'un de donnee dans un document inexistant");
		$nodes = array();
		for($i=0; $i<$this->queryTarget->length; $i++) $nodes[] = $this->queryTarget->item($i);
		$this->queryTarget = NULL;
		return $nodes;
    }


	public function insert($data, $pos=NULL)
	{
		if(!isset($this->document))throw new \Exception("import d'un de donnee dans un document inexistant");
		for($i=0;$i<$this->queryTarget->length;$i++)
		{
			$node = $this->queryTarget->item($i);
			switch(get_class($node))
			{
				case \DOMElement::class:
					$importedNode = $this->import($data);
					if(!is_object($importedNode) || !is_subclass_of($importedNode, \DOMNode::class)) throw new \Exception("Import de donne de type ".gettype($importedNode)." impossible");//TODO: c'est le boulot de la methode import()
					if(!isset($pos)||$pos<1)
					{
						$node->appendChild($this->document->importNode($importedNode, true));
					}
					else
					{
						for($target=$node->firstChild,$j=1;isset($target)&&$j<$pos;$j++,$target=$target->nextSibling);
						$node->insertBefore($this->document->importNode($importedNode, true), $target);
					}
					break;
				case \DOMAttr::class:
					//TODO: verifier le type de l'element
					$node->value .= " ".$data;
					break;
				default:
					throw new \Exception("Les noeuds du type \"".get_class($node)."\" ne sont pas gerer par le moteur de template");
					break;
			}
		}
		return $this;
	}

	public function insertAttribute($name, $value)
	{
		if(!isset($this->document))throw new \Exception("import d'un de donnee dans un document inexistant");
		$domAttribute = $this->document->createAttribute($name);
		$domAttribute->value = $value;
		for($i=0; $i<$this->queryTarget->length; $i++)
		{
			$element = $this->queryTarget->item($i);
			$element->appendChild($domAttribute);
		}
		$this->queryTarget = NULL;
		//return $this;
	}

	public function setAttribute($name, $value)
	{
		if(!isset($this->document))throw new \Exception("import d'un de donnee dans un document inexistant");
		for($i=0; $i<$this->queryTarget->length; $i++)
		{
			$element = $this->queryTarget->item($i);
			if($element->nodeType==1)$element->setAttribute($name, $value);
		}
	}

    public function select($xpath=NULL)
	{
		if(!isset($this->document))throw new \Exception("import d'un de donnee dans un document inexistant");
		$this->query = $xpath;
		if(!empty($this->query))$this->queryTarget = $this->xpath->query($this->query);
		else $this->queryTarget = NULL;
		return $this;
	}

	protected function writeText($content)
	{
        if(!$this->queryTarget->length) throw new \Exception("la requete \"".$this->query."\" ne retourne aucun element.");
        for($i=0; $i<$this->queryTarget->length; $i++)
		{
			$element = $this->queryTarget->item($i);
            switch(get_class($element))
            {
                case "DOMAttr":
                    $element->value = $content;
                    break;
                case "DOMElement":
                    $nodeToErase = array();
                    for($ptr=$element->firstChild;isset($ptr);$ptr=$ptr->nextSibling)$nodeToErase[]=$ptr;
                    for($i=0;$i<count($nodeToErase);$i++)$element->removeChild($nodeToErase[$i]);
                    $textNode = new DomText($content);
                    $element->appendChild($textNode);
                    break;
                case "DOMText":
                    $element->data = $content;
                    break;
                default:
                    throw new \Exception("Impossible d'ecrire du texte dans un objet \"".get_class($element)."\".");
                    break;
            }
		}
		$this->queryTarget = NULL;
	}


	///!Routines privées
	private function import($source)
	{
		if(!isset($this->document))throw new \Exception("import d'un de donnee dans un document inexistant");
		if(empty($this->document->documentElement))
		{
			//echo"<pre>".htmlentities(priNODE_TYPE_r($source, TRUE))."</pre>";die();
			$this->document->loadXML($this->removeIndent($source));
			return;
		}

		$docType = "UNDEFINED";
		$element = NULL;
		if(is_string($source))
		{
			$source = trim($source);
			$docType = preg_match('|<!doctype[^>]*>|i', $source, $dtype) ?
				"DOCTYPED"
				:
				(
					(preg_match('|<[^>]*>|', $source)) ?
						"TEMPLATE" //TEMPLATE pour element complet -> broken Template = procedure de reconstruction
						:
						"TEXT" //avec ou sans caractere speciaux
				)
				;

			/*
			$docType = (preg_match('|<[^>]*>|', $source)) ?
				//"XML"
				preg_match("|^<([^>]*)>|", $source, $stag) &&  preg_match("|<([^>]*)>$|", $source, $etag) ? priNODE_TYPE_r($stag[1], TRUE)."--".priNODE_TYPE_r($etag[1], TRUE) : "TOTO"
				:
				"TEXT";
			*/
			//$isXmlStart = (preg_match('|^<xml>|i', $source, $match)) ? TRUE : FALSE;
			//$isHtml;
			//$docType = "HTML";
			//$docType = (!$isXmlStart /*&& !empty($dtd)*/) ? "TEMPLATE" : $docType;
		}
		elseif(is_object($source))
		{
			$docType = "NODE";
		}

		//echo "<fieldset><legend>".$docType." | ".preg_match('|<!doctype[^>]*>|i', $source, $dtype)."</legend><textarea cols='150' rows='20'>".$source."\n*******\n".""."</textarea></fieldset>";
		switch($docType)
		{
			case "NODE":
				$element = $source;
				break;
			case "TEMPLATE":
				$doc = new DOMDocument("1.0", "iso-8859-1");
				$doc->preserveWhiteSpace = FALSE;
				$doc->formatOutput = true;
				$doc->loadXML("<xml>".html_entity_decode($source)."</xml>");
				$element = $doc->firstChild->firstChild;
				break;
			case "TEXT":
				$element = $this->document->createTextNode(html_entity_decode($source));
				break;
			case "DOCTYPED":
				//$this->document->loadHTML($this->removeIndent($source));
				break;
			default:
				throw new \Exception("type de donne importe non reconnu : ".$docType."");
				break;
		}

		//echo "<br/><pre>";var_dump($docType, htmlentities($source), $element);echo"</pre>";

		return $element;
	}

	private function nodeTypeName($nodeType)
	{
		$nodeTypeName = array(
			"NONE",
			"ELEMENT",
			"ATTRIBUTE",
			"TEXT",
			"CDATA",
			"ENTITY_REF",
			"ENTITY",
			"PI",
			"COMMENT",
			"DOC",
			"DOC_TYPE",
			"DOC_FRAGMENT",
			"NOTATION",
			"WHITESPACE",
			"SIGNIFICANODE_TYPE_WHITESPACE",
			"END_ELEMENT",
			"END_ENTITY",
			"XML_DECLARATION"
		);
		return $nodeTypeName[$nodeType];
	}

	private function removeIndent($template)
	{
		$template = str_replace("\t", "", $template);
		$template = str_replace("\r", "", $template);
		$template = str_replace("\n", "", $template);
		$template = preg_replace("|(<[^>]+>)\s*(<[^>]+>)|", "$1$2", $template);
		$template = preg_replace("|(<[^>]+>)\s*(<[^>]+>)|", "$1$2", $template);
		return $template;
	}

	/*** OLD ***/

	///!
	public function insertXml($content, $target=NULL)
	{

		$this->import($content);
		echo "\n<p>fin de methode</p>";exit();
		if(is_string($content))
		{
			$doc = new DOMDocument();
			$doc->loadXML("<template>".$content."</template>");
		}
		else $doc = $content;


		if(isset($target))
		{
			foreach($doc->firstChild->childNodes as $childNode)
			{
				$target->appendChild($this->ptrDoc->importNode($childNode, true));
			}
		}
		else for($i=0; $i<$this->queryTarget->length; $i++)
		{
			$element = $this->queryTarget->item($i);
			switch($this->nodeTypeName($element->nodeType))
			{
				case "ELEMENT":
						$elementsToRemove = array();
						foreach($element->childNodes as $childNode) $elementsToRemove[] = $childNode;
						foreach($elementsToRemove as $elementToRemove) $element->removeChild($elementToRemove);
						foreach($doc->firstChild->childNodes as $childNode)
						{
							$element->appendChild($this->ptrDoc->importNode($childNode, true));
						}
					break;
				default:
					echo "<p>Les noeuds du type \"".$this->nodeTypeName($element->nodeType)."\" ne sont pas gerer par le moteur de template</p>";
					break;
			}
		}
		$this->queryTarget = NULL;
		return $this;
	}

	/*
	private $template = "";
	protected $defaultTemplate = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\"><html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\" lang=\"fr\"><head><title></title></head><body></body></html>";

	private $title;
	private $element;
	private $import = array();

	private $isElementSelected 	= FALSE;
	private $ptrDoc;
	private $stack = array();

	///!
	public function importXml($source)
	{
		$source = $this->removeIndent("<xml>".trim($source)."</xml>");
		$this->import[$this->element] = array("type"=>NULL, "doc"=>NULL, "path"=>NULL);
		$this->import[$this->element]["type"] = "xml";
		$this->import[$this->element]["doc"] = new DOMDocument();
		$this->import[$this->element]["doc"]->preserveWhiteSpace = FALSE;
		$this->import[$this->element]["doc"]->loadXML($source);
		$this->import[$this->element]["path"] = new DOMXpath($this->import[$this->element]["doc"]);
		$this->element = NULL;
	}

	///!
	public function getSrc()
	{
		if(!$this->isDocCreated)$this->createDocument();
		if(!$this->isElementSelected)
		{
			if(!$this->isDocCreated)$this->createDocument();
			foreach($this->stack as $instruction)
			{
				call_user_func_array(array($this, $instruction[0]), array($instruction[1], $instruction[2]));
			}
			$result = $this->document->saveHTML();
		}
		else $result = $this->document->saveXML($this->queryTarget->item(0));
		$this->isElementSelected = FALSE;
		return $result;
	}

	///!
	public function count($xpath=NULL)
	{
		if(!$this->isDocCreated)$this->createDocument();
		if (isset($xpath))
		{
			$targets = $this->xpath->query($xpath);
			$count = $targets->length;
		}
		else $count = $this->queryTarget->length;
		$this->isElementSelected = FALSE;
		return $count;
	}

	///!
	public function getElementType($element)
	{
		$elementType = "undefined";
		$elementType = (is_string($element) && (strstr($element, "<")||strstr($element, "<"))) ? "xml" : $elementType;
		$this->isElementSelected = FALSE;
		return $elementType;
	}

	public function getContent()
	{
		if(!$this->isDocCreated)$this->createDocument();
		$result = array();
		for($i=0; $i<$this->queryTarget->length; $i++)
		{
			$element = $this->queryTarget->item($i);
			switch($this->nodeTypeName($element->nodeType))
			{
				case "ELEMENT":
					$content = "";
					for($ptrTarget=$element->firstChild; $ptrTarget; $ptrTarget=$ptrTarget->nextSibling)
					{
						switch($this->nodeTypeName($ptrTarget->nodeType))
						{
							case "CDATA":
								$content .= substr($this->document->saveXML($ptrTarget), 9, -3);
								break;
						}
					}
					$result[] = $content;
					break;
				case "ATTRIBUTE":
					$result[] = $element->value;
					break;
				case "TEXT":
					$result[] = $element->wholeText;
					break;
				default:
					throw new Exception("<p>Les noeuds du type \"".$this->nodeTypeName($element->nodeType)."\" ne sont pas gerer par le moteur de template</p>");
					break;
			}
		}
		$this->isElementSelected = FALSE;
		return $result;
	}

	//******************************************************************************************************************

	//
	//insertion attribut
	//case "DOMAttr":
	//				$target->value .= $content;
	//				break;
	//
	//

	public function setSrc($src)
	{
		$this->template = $src;
		if(!$this->isDocCreated)$this->createDocument();
	}

	public function write($content)
	{
		if(is_string($content))
		{
			$doc = new DOMDocument();
			$doc->loadXML("<template>".$content."</template>");
		}
		else $doc = $content;

		for($i=0; $i<$this->queryTarget->length; $i++)
		{
			$element = $this->queryTarget->item($i);
			switch($this->nodeTypeName($element->nodeType))
			{
				case "ELEMENT":
						$elementsToRemove = array();
						foreach($element->childNodes as $childNode) $elementsToRemove[] = $childNode;
						foreach($elementsToRemove as $elementToRemove) $element->removeChild($elementToRemove);
						foreach($doc->firstChild->childNodes as $childNode)
						{
							$element->appendChild($this->ptrDoc->importNode($childNode, true));
						}
					break;
				default:
					echo "<p>Les noeuds du type \"".self::$nodeTypeName[$element->nodeType]."\" ne sont pas gerer par le moteur de template</p>";
					break;
			}
		}
		return $this;
	}

	public function writeIn($xpath, $content)
	{
		$this->escape($content);

		if(!$this->isDocCreated)
		{
			array_push($this->stack, array(__METHOD__, $xpath, $content));
			return;
		}
		$targets = $this->xpath->query($xpath);
		for($i=0;$i<$targets->length;$i++)
		{
			$target = $targets->item($i);
			$nodeType = get_class($target);
			switch($nodeType)
			{
				case "DOMAttr":
					$target->value = $content;
					break;
				case "DOMElement":
					$doc = new DOMDocument();
					$doc->loadXML($content);
					$targetChilds = $target->childNodes;
					foreach($targetChilds as $targetChild)
					{
						$target->removeChild($targetChild);//var_dump("remove !!!: ".$xpath);
					}
					$target->appendChild($this->document->importNode($doc->firstChild, true));
					break;
				default:
					throw new Exception("Le type de noeud \"".$nodeType."\" n'est pas gere.");
					break;
			}
		}
	}

	public function insertIn($xpath, $content, $pos="end")
	{
		$this->escape($content);

		if(!$this->isDocCreated)
		{
			array_push($this->stack, array(__METHOD__, $xpath, $content));
			return;
		}
		$targets = $this->xpath->query($xpath);
		for($i=0;$i<$targets->length;$i++)
		{
			$target = $targets->item($i);
			$nodeType = get_class($target);
			switch($nodeType)
			{
				case "DOMAttr":
					$target->value .= $content;
					break;
				case "DOMElement":
					$doc = new DOMDocument();
					$doc->loadXML($content);
					$targetChilds = $target->childNodes;
					$target->appendChild($this->document->importNode($doc->firstChild, true));
					break;
				default:
					throw new Exception("Le type de noeud \"".$nodeType."\" n'est pas gere.");
					break;
			}
		}
	}

	public function insertTextIn($xpath, $content)
	{
	}

	public function insertAttributeIn($xpath, $name, $value)
	{
		$this->escape($value);

		if(!$this->isDocCreated)
		{
			array_push($this->stack, array(__METHOD__, $xpath, $name, $value));
			return;
		}

		$domAttribute = $this->document->createAttribute($name);
		$domAttribute->value = $value;
		$targets = $this->xpath->query($xpath);
		for($i=0;$i<$targets->length;$i++)
		{
			$target = $targets->item($i);
			$target->appendChild($domAttribute);
		}
	}

	public function insertCDataIn($xpath, $content)
	{
		if(!$this->isDocCreated)
		{
			array_push($this->stack, array(__METHOD__, $xpath, $content));
			return;
		}
		$targets = $this->xpath->query($xpath);
		$target = $targets->item(0);
		$target->appendChild($this->document->createCDATASection($content));
	}

	public function appendText($xpath, $content)
	{
		if(!$this->isDocCreated)
		{
			array_push($this->stack, array(__METHOD__, $xpath, $content));
			return;
		}
		$targets = $this->xpath->query($xpath);
		for($i=0;$i<$targets->length;$i++)
		{
			$target = $targets->item($i);
			if(get_class($target)!="DOMElement") throw new Exception("erreur test ...");
			$target->appendChild(new DOMText($content));
		}
	}

	private function escape($content)//TODO: echapper les balises inclues dans le code javaascript du template
	{
		$isHooked = TRUE;
		//preg_match("|<script.*|", $content, $match);
		//echo "<p>".htmlentities(priNODE_TYPE_r($content, TRUE))."</p>";
		//if(empty($match))return;
		//echo "<textarea cols='100' rows=10>".priNODE_TYPE_r($match, TRUE)."</textarea>";
		return $isHooked;
	}
	*/
}
?>
