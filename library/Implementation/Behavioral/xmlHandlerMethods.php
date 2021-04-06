<?php
namespace Oculie\Core\Implementation;
/*
XML_ELEMENT_NODE (integer)	1	Node is a DOMElement
XML_ATTRIBUTE_NODE (integer)	2	Node is a DOMAttr
XML_TEXT_NODE (integer)	3	Node is a DOMText
XML_CDATA_SECTION_NODE (integer)	4	Node is a DOMCharacterData
XML_ENTITY_REF_NODE (integer)	5	Node is a DOMEntityReference
XML_ENTITY_NODE (integer)	6	Node is a DOMEntity
XML_PI_NODE (integer)	7	Node is a DOMProcessingInstruction
XML_COMMENT_NODE (integer)	8	Node is a DOMComment
XML_DOCUMENT_NODE (integer)	9	Node is a DOMDocument
XML_DOCUMENT_TYPE_NODE (integer)	10	Node is a DOMDocumentType
XML_DOCUMENT_FRAG_NODE (integer)	11	Node is a DOMDocumentFragment
XML_NOTATION_NODE (integer)	12	Node is a DOMNotation
XML_HTML_DOCUMENT_NODE (integer)	13	 
XML_DTD_NODE (integer)	14	 
XML_ELEMENT_DECL_NODE (integer)	15	 
XML_ATTRIBUTE_DECL_NODE (integer)	16	 
XML_ENTITY_DECL_NODE (integer)	17	 
XML_NAMESPACE_DECL_NODE (integer)	18	 
XML_ATTRIBUTE_CDATA (integer)	1	 
XML_ATTRIBUTE_ID (integer)	2	 
XML_ATTRIBUTE_IDREF (integer)	3	 
XML_ATTRIBUTE_IDREFS (integer)	4	 
XML_ATTRIBUTE_ENTITY (integer)	5	 
XML_ATTRIBUTE_NMTOKEN (integer)	7	 
XML_ATTRIBUTE_NMTOKENS (integer)	8	 
XML_ATTRIBUTE_ENUMERATION (integer)	9	 
XML_ATTRIBUTE_NOTATION (integer)
*/

trait xmlHandlerMethods
{
	public function append($content)
	{
		$this->insert($content, -1);
	}
	
	public function delete()//TODO: effacer les attributs par Structural matchin //TODO: creer un objet patternMatching
	{
		for($i=0;$i<$this->xmlHandler["target"]->count();$i++)
		{
			$this->xmlHandler["target"]->item($i)->parentNode->removeChild($this->xmlHandler["target"]->item($i));
		}
		//var_dump($this->xmlHandler["target"]->count());
	}
	
	public function asText()
	{		
		$getAsText = function($object)
		{
			$value = NULL;
			$nodeType = is_object($object) ? $object->nodeType : "NONE";
			switch($nodeType)
			{
				case XML_ATTRIBUTE_NODE:
					$value = $object->value;
					break;
			}
			return $value;
		};
		
		if(is_array($this->xmlHandler["response"]))
		{
			$response = "";
			foreach($this->xmlHandler["response"] as $domObject)
			{
				$response .= $getAsText($domObject);
			}
		}
		else
		{
			$response = "";
			$response .= $getAsText($this->xmlHandler["response"]);
		}
		//$this->xmlHandler = NULL;
		
		return $response;
	}
	
	public function getAsDOM()
	{
		$dom = NULL;
		if(!empty($this->xmlHandler["target"]))
		{
			if($this->xmlHandler["target"]->count()>1) throw new \Exception("Impossible de renvoyer le code source d'une liste d'objet");
			$dom = $this->xmlHandler["target"]->item(0);
		}
		else
		{
			$dom = $this->xmlHandler["document"];
		}
		$this->xmlHandler["target"] = NULL;
		return $dom;
	}
	
	public function getAsXML()
	{
		$xml = NULL;
		$isEmptyTarget = (isset($this->xmlHandler["target"]) && $this->xmlHandler["target"]) ? FALSE : TRUE;
		$dom = $this->getAsDOM();
		if($isEmptyTarget)
		{
			$xml = $dom->saveXML();
		}
		else
		{
			$xml = $this->xmlHandler["document"]->saveXML($dom);
		}
		return $xml;
	}
	
	public function getContent()
	{
		$content = NULL;
		if(empty($this->xmlHandler["target"]))
		{
			$content = $this->xmlHandler["document"];
		}
		elseif(!$this->xmlHandler["target"]->count())
		{
			$content = NULL;
			
		}
		elseif(!$this->xmlHandler["target"]->count()<1)
		{
			$content = $this->xmlHandler["target"]->item(0);
		}
		else
		{
			$content = [];
			for($i=0; $i<$this->xmlHandler["target"]->count(); $i++) $content[] = $this->xmlHandler["target"]->item($i);		
		}
		$this->xmlHandler["response"] = $content;
		return $this;
	}
	
	public function getObjectNumber()
	{
		return $this->xmlHandler["target"]->count();
	}
	
	public function loadSource($xmlContent)
	{
		$this->xmlHandler["document"] 	= new \DOMDocument("1.0", "iso-8859-1");
		$this->xmlHandler["document"]->preserveWhiteSpace = FALSE;
		$this->xmlHandler["document"]->formatOutput = true;
		$this->xmlHandler["document"]->loadXML($xmlContent);
		$this->xmlHandler["request"] 	= "";
		$this->xmlHandler["response"] 	= NULL;
		$this->xmlHandler["parser"] 	= new \DOMXpath($this->xmlHandler["document"]);
		return $this;
	}
	
	public function insert($content, $pos=-1)
	{
		if(!isset($this->xmlHandler["document"]))throw new \Exception("import de donnee dans un document inexistant");
		
		if(is_string($content))
		{
			$doc = new \DOMDocument();
			$doc->preserveWhiteSpace = FALSE;
			$doc->formatOutput = true;
			$doc->loadXML($content);
			$content = $doc->firstChild;
		}
		
		if(!is_object($content) || !is_subclass_of($content, \DOMNode::class)) throw new \Exception("Import de donne de type ".gettype($content)." impossible");
		$content = $this->xmlHandler["document"]->importNode($content, TRUE);
		
		for($i=0; $i<$this->xmlHandler["target"]->count();$i++)
		{
			$target = $this->xmlHandler["target"]->item($i);
			switch($target->nodeType)
			{
				case XML_ELEMENT_NODE:
					if($pos<0)$target->appendChild($content);
					else{}
					break;
				default:
					break;
			}
		}
		$this->xmlHandler["target"] = NULL;
		$this->xmlHandler["request"] = NULL;
		return $this;
	}
	
	public function write($text)
	{
		//TODO controller le contenu du text pour CDATA ...
		return $this->writeText($text);
	}
	
	public function writeComment($text)
	{
	}
	
	public function writeCDATA($text)
	{	
	}
	
	public function writeText($text)
	{
		if(!isset($this->xmlHandler["document"]))throw new \Exception("import de donnee dans un document inexistant");
		foreach($this->xmlHandler["target"] as $target)
		{
			switch($target->nodeType)
			{
				case XML_ELEMENT_NODE:
					if($target->hasChildNodes()) for($i=$target->firstChild;isset($i);$i=$i->nextSibling) $target->removeChild($i);
					$target->appendChild($this->xmlHandler["document"]->createTextNode($text));
					break;
			}
		}
		$this->xmlHandler["target"] = NULL;
		$this->xmlHandler["request"] = NULL;
		return $this;
	}
	
	public function xmlHandlerInit()
	{
		if(!isset($this->xmlHandler))
		{
			$this->xmlHandler = [
				"document"	=> NULL,
				"parser"	=> NULL,
				"target"	=> NULL
			];
		}
	}
}
?>
