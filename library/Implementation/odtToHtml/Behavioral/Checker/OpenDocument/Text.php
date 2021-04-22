<?php
namespace Oculie\OdtToHtml\Checker\OpenDocument;

use Oculie\Core\Definition\Xml as XmlDefinition;

class Text
{
	public static function isDocumentStart($node)
	{
		return $node->type==XmlDefinition::OPEN_TAG&&$node->name=="office:document-content";
	}

	public static function isDocumentEnd($node)
	{
		return $node->type==XmlDefinition::CLOSE_TAG&&$node->name=="office:document-content";
	}

	public static function isElementAnchorOpenTag($node)
	{
		return $node->type==XmlDefinition::OPEN_TAG_END&&$node->name=="text:a";
	}

	public static function isElementAnchorCloseTag($node)
	{
		return $node->type==XmlDefinition::CLOSE_TAG&&$node->name=="text:a";
	}

	public static function isElementBodyOpenTag($node)
	{
		return $node->type==XmlDefinition::OPEN_TAG&&$node->name=="office:body";
	}

	public static function isElementBodyCloseTag($node)
	{
		return $node->type==XmlDefinition::CLOSE_TAG&&$node->name=="office:body";
	}

	public static function isElementImageEmptyTag($node)
	{
		return $node->type==XmlDefinition::EMPTY_TAG_END&&$node->name=="draw:image";
	}

	public static function isElementParagraphOpenTag($node)
	{
		return $node->type==XmlDefinition::OPEN_TAG&&$node->name=="text:p";
	}

	public static function isElementParagraphCloseTag($node)
	{
		return $node->type==XmlDefinition::CLOSE_TAG&&$node->name=="text:p";
	}

	public static function isElementHeadingOpenTag($node)
	{
		return $node->type==XmlDefinition::OPEN_TAG_END&&$node->name=="text:h";
	}

	public static function isElementHeadingCloseTag($node)
	{
		return $node->type==XmlDefinition::CLOSE_TAG&&$node->name=="text:h";
	}

	public static function isText($node)
	{
		return $node->type==XmlDefinition::TEXT;
	}
}