<?php
namespace Oculie\Core\Definition;

use Oculie\Core\Definition\Xml as XmlDefinition;

class Xml
{
	const NONE				= 0;

	const OPEN_TAG			= 1;
	const OPEN_TAG_START	= 2;
	const OPEN_TAG_END		= 3;
	const EMPTY_TAG			= 4;
	const EMPTY_TAG_START	= 5;
	const EMPTY_TAG_END		= 6;
	const CLOSE_TAG			= 7;
	const ATTRIBUTE			= 8;
	const TEXT				= 9;
	const CDATA				= 10;
	const ENTITY_REF		= 11;
	const PI				= 12;
	const COMMENT			= 13;
	const DOCTYPE			= 14;
	const INDENTATION		= 15;

	const NODE_TYPE_NAME			= [
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
		"SIGNIFICANT_WHITESPACE",
		"END_ELEMENT",
		"END_ENTITY",
		"XML_DECLARATION"
	];

	const TAG_TYPE			= [
		"NONE"							=> 0,
		"OPEN"							=> 1,
		"CLOSE"							=> 2,
		"AUTO_CLOSE"					=> 3
	];

	const TAG_TYPE_NAME		= [
		"NONE",
		"OPEN",
		"CLOSE",
		"AUTO_CLOSE"
	];

	const TOKEN_TYPE		 = [
		"ELEMENT"						=> 1,
		"AUTO_CLOSE_ELEMENT"			=> 2,
		"END_ELEMENT"					=> 3,
		"ATTRIBUTE"						=> 4,
		"TEXT"							=> 5,
		"CDATA"							=> 6,
		"PI"							=> 7,
		"COMMENT"						=> 8,
		"DOCTYPE"						=> 9,
		"INDENTATION"					=> 10,
		"UNDEFINED"						=> 99
	];

	const tokenName = [
		XmlDefinition::OPEN_TAG			=> "OPEN_TAG",
		XmlDefinition::OPEN_TAG_START	=> "OPEN_TAG",
		XmlDefinition::OPEN_TAG_END		=> "OPEN_TAG",
		XmlDefinition::CLOSE_TAG		=> "CLOSE_TAG",
		XmlDefinition::EMPTY_TAG		=> "EMPTY_TAG",
		XmlDefinition::EMPTY_TAG_START	=> "EMPTY_TAG",
		XmlDefinition::EMPTY_TAG_END	=> "EMPTY_TAG",
		XmlDefinition::ATTRIBUTE		=> "ATTRIBUTE",
		XmlDefinition::TEXT				=> "TEXT",
		XmlDefinition::CDATA			=> "CDATA",
		XmlDefinition::ENTITY_REF		=> "ENTITY_REF",
		XmlDefinition::PI				=> "PI",
		XmlDefinition::COMMENT			=> "COMMENT",
		XmlDefinition::DOCTYPE			=> "DOCTYPE",
		XmlDefinition::INDENTATION		=> "INDENTATION"
	];
}