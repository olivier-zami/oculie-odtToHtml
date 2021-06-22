<?php
namespace Oculie\OdtToHtml\Checker\OpenDocument;

use Oculie\Core\Definition\Xml as XML_DEFINITION;

class Text
{
	/*
	 * Document
	 */
	public static function isDocumentStart($node): bool
	{
		return $node->type==\XMLReader::ELEMENT&&$node->name=="office:document-content"&&$node->tagType==XML_DEFINITION::TAG_TYPE["OPEN"];
	}

	public static function isDocumentEnd($node): bool
	{
		return $node->type==\XMLReader::END_ELEMENT&&$node->name=="office:document-content"&&$node->tagType==XML_DEFINITION::TAG_TYPE["CLOSE"];
	}

	/*
	 * tag : <a>
	 */
	public static function isElementAnchorOpenTag($node): bool
	{
		return $node->type==\XMLReader::ELEMENT&&$node->tagType==XML_DEFINITION::TAG_TYPE["OPEN"]&&$node->name=="text:a";
	}

	public static function isElementAnchorCloseTag($node): bool
	{
		return $node->type==\XMLReader::END_ELEMENT&&$node->tagType==XML_DEFINITION::TAG_TYPE["CLOSE"]&&$node->name=="text:a";
	}

	/*
	 * tag : <body>
	 */
	public static function isElementBodyOpenTag($node): bool
	{
		return $node->type==\XMLReader::ELEMENT&&$node->tagType==XML_DEFINITION::TAG_TYPE["OPEN"]&&$node->name=="office:body";
	}

	public static function isElementBodyCloseTag($node): bool
	{
		return $node->type==\XMLReader::END_ELEMENT&&$node->tagType==XML_DEFINITION::TAG_TYPE["CLOSE"]&&$node->name=="office:body";
	}

	/*
	 * tag : <h>
	 */
	public static function isElementHeadingOpenTag($node): bool
	{
		return $node->type==\XMLReader::ELEMENT&&$node->tagType==XML_DEFINITION::TAG_TYPE["OPEN"]&&$node->name=="text:h";
	}

	public static function isElementHeadingCloseTag($node): bool
	{
		return $node->type==\XMLReader::END_ELEMENT&&$node->tagType==XML_DEFINITION::TAG_TYPE["CLOSE"]&&$node->name=="text:h";
	}

	/*
	 * tag : <img>
	 */
	public static function isElementImageEmptyTag($node): bool
	{
		return $node->type==\XMLReader::ELEMENT&&$node->tagType==XML_DEFINITION::TAG_TYPE["AUTO_CLOSE"]&&$node->name=="draw:image";
	}

	/*
	 * tag : <li>
	 */
	public static function isElementListItemOpenTag($node): bool
	{
		return $node->type==\XMLReader::ELEMENT&&$node->tagType==XML_DEFINITION::TAG_TYPE["OPEN"]&&$node->name=="text:list-item"&&$node->parentName=="text:list";
	}

	public static function isElementListItemCloseTag($node): bool
	{
		return $node->type==\XMLReader::END_ELEMENT&&$node->tagType==XML_DEFINITION::TAG_TYPE["CLOSE"]&&$node->name=="text:list-item"&&$node->parentName=="text:list";
	}

	/*
	 * tag : <p>
	 */
	public static function isElementParagraphOpenTag($node): bool
	{
		return $node->type==\XMLReader::ELEMENT&&$node->tagType==XML_DEFINITION::TAG_TYPE["OPEN"]&&$node->name==="text:p"&&!in_array($node->parentName, [
			"text:list-item"
		]);
	}

	public static function isElementParagraphCloseTag($node): bool
	{
		return $node->type==\XMLReader::END_ELEMENT&&$node->tagType==XML_DEFINITION::TAG_TYPE["CLOSE"]&&$node->name=="text:p"&&!in_array($node->parentName, [
			"text:list-item"
		]);
	}

	/*
	 * tag : <section>
	 */
	public static function isElementSectionOpenTag($node): bool
	{
		return $node->type==\XMLReader::ELEMENT&&$node->tagType==XML_DEFINITION::TAG_TYPE["OPEN"]&&$node->name=="text:section";
	}

	public static function isElementSectionCloseTag($node): bool
	{
		return $node->type==\XMLReader::END_ELEMENT&&$node->tagType==XML_DEFINITION::TAG_TYPE["CLOSE"]&&$node->name=="text:section";
	}

	/*
	 * tag : <span>
	 */
	public static function isElementSpanLiOpenTag($node): bool
	{
		return $node->type==\XMLReader::ELEMENT&&$node->name=="text:p"&&/*$node->tagType==XML_DEFINITION::TAG_TYPE["OPEN"]&&*/$node->parentName=="text:list-item";
	}

	public static function isElementSpanLiCloseTag($node): bool
	{
		return $node->type==\XMLReader::END_ELEMENT&&$node->name=="text:p"&&$node->tagType==XML_DEFINITION::TAG_TYPE["CLOSE"]&&$node->parentName=="text:list-item";
	}

	/*
	 * tag : <ul>
	 */
	public static function isElementListOpenTag($node): bool
	{
		return $node->type==\XMLReader::ELEMENT&&$node->tagType==XML_DEFINITION::TAG_TYPE["OPEN"]&&$node->name=="text:list";
	}

	public static function isElementListCloseTag($node): bool
	{
		return $node->type==\XMLReader::END_ELEMENT&&$node->tagType==XML_DEFINITION::TAG_TYPE["CLOSE"]&&$node->name=="text:list";
	}

	/*
	 * text
	 */
	public static function isText($node): bool
	{
		return $node->type==\XMLReader::TEXT;
	}

	/*
	 * test ...
	 */
	public static function test($node): bool
	{
		return $node->type==\XMLReader::ELEMENT||$node->type==\XMLReader::END_ELEMENT;
		//return $node->name==="text:p";
		//return $node->name=="text:p"/*&&$node->tagType==XML_DEFINITION::TAG_TYPE["OPEN"]*/&&$node->parentName=="text:list-item";
		//return $node->type==\XMLReader::ELEMENT&&$node->name=="text:p"&&$node->tagType==XML_DEFINITION::TAG_TYPE["OPEN"]&&$node->parentName=="text:list-item";
		//return $node->type==\XMLReader::END_ELEMENT&&$node->name=="text:p"&&$node->tagType==XML_DEFINITION::TAG_TYPE["CLOSE"]&&$node->parentName=="text:list-item";
	}
}