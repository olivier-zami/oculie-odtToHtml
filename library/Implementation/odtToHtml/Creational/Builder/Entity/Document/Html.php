<?php
namespace Oculie\OdtToHtml\Builder\Entity\Document;

class Html
{
	/*
	 * Interface
	 */

	public static function create(): Html
	{
		if(!isset(self::$builder))self::$builder = new Html();
		return self::$builder;
	}

	public function __construct()
	{
		$this->xmlWriter = new \XMLWriter();
		$this->xmlWriter->openMemory();
		$this->xmlWriter->setIndent(TRUE);
	}

	public function setOdtDocument($odtDocument)
	{
		self::$odtDocument = $odtDocument;
	}

	public function getInstance(): \Oculie\Core\Data\Entity\Document
	{
		$instance = new \Oculie\Core\Data\Entity\Document();
		$instance->setContent($this->xmlWriter->outputMemory());
		return $instance;
	}

	public function startDocument($node)
	{
		$this->xmlWriter->startDocument("1.0");
		$this->xmlWriter->startElement("html");
		$this->xmlWriter->startElement("head");
	}

	public function endDocument($node)
	{
		$this->xmlWriter->endElement();
		$this->xmlWriter->endDocument();
	}

	public function addElementAnchorOpenTag($node)
	{
		$this->xmlWriter->startElement("a");
		if(isset($node->attribute["xlink:href"]))
		{
			$this->xmlWriter->startAttribute("href");
			$this->xmlWriter->text($node->attribute["xlink:href"]);
			$this->xmlWriter->endAttribute();
		}
	}

	public function addElementBodyOpenTag($node)
	{
		$this->xmlWriter->endElement();
		$this->xmlWriter->startElement("body");
	}

	public function addElementHeadingOpenTag($node)
	{
		$this->xmlWriter->startElement("h".(isset($node->attribute["text:outline-level"])?($node->attribute["text:outline-level"]>6?6:$node->attribute["text:outline-level"]):""));
	}

	public function addElementImageEmptyTag($node)
	{
		$this->xmlWriter->startElement("img");
		if(strstr($node->attribute["xlink:href"], "ObjectReplacements"))//TODO: find a way to handle ObjectReplacements file (VCLMTF format)
		{
			$src = "";
		}
		else
		{
			$src = 'data:image;base64,'.base64_encode(self::$odtDocument->get($node->attribute["xlink:href"]));
		}
		$this->xmlWriter->startAttribute("src");
		$this->xmlWriter->text($src);
		$this->xmlWriter->endAttribute();
		$this->xmlWriter->endElement();
	}

	public function addElementParagraphOpenTag($node)
	{
		$this->xmlWriter->startElement("p");
	}

	public function addText($node)
	{
		$this->xmlWriter->text($node->value);
	}

	public function closeElement($node)
	{
		$this->xmlWriter->endElement();
	}

	/*
	 * Routines & Properties
	 */

	private static $builder;
	private static $odtDocument;

	private $xmlWriter;
}