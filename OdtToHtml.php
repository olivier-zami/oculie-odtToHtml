<?php
namespace Oculie;

use Oculie\OdtToHtml\Builder\Entity\Document\Html as HtmlBuilder;
use Oculie\Core\Definition\Xml as XML_DEFINITION;
use Oculie\Core\Builder\Callback as ActionBuilder;

class OdtToHtml extends \Oculie\Core\Extension
{
    /* Configuration.
    0 : do not parse, do not print
    1 : print as simple text (do not apply any HTML tag or style)
    2 : print  and apply all supported HTML tags and styles
    */

    public static function ophir_is_image ($file): bool
    {
        $image_extensions = array("jpg", "jpeg", "png", "gif", "svg");
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (!in_array($ext, $image_extensions)) return FALSE;
        return (strpos(@mime_content_type($file), 'image') === 0);
    }

    public static function ophir_copy_file($from, $to) {
        if (function_exists('file_unmanaged_copy'))
        {
            $filename = file_unmanaged_save_data(file_get_contents($from), $to, FILE_EXISTS_REPLACE);
            return ($filename) ? file_create_url($filename) : false;
        }else {
            if (file_exists($to)) {
                if (crc32(file_get_contents($from)) === crc32(file_get_contents($from))) return $to;
                $i = pathinfo($to);
                $to = $i['dirname'] . '/' . $i['filename'] . time() . '.' . $i['extension'];
            }
            return (copy($from, $to)) ? $to : FALSE;
        }
    }

    public static function ophir_error($error)
    {
        if (function_exists("drupal_set_message")){
            drupal_set_message($error, 'error');
        }else{
            echo '<div style="color:red;font-size:2em;">' . $error . '</div>';
        }
    }
    
    public static function parse($odtDocument)
    {
        $content = NULL;
        if(is_string($odtDocument))
        {
            $content = $odtDocument;
        }
        else if(is_object($odtDocument))//TODO check for subclass type
        {
            $content = $odtDocument->get("content.xml");
        }

        if(!isset(self::$parser))self::$parser = new \Oculie\Core\Parser\Xml();//TODO: etendre la classe avec le trait setEventAction (interface onEvent()->execute() comme technique alternative)
		self::$htmlBuilder = HtmlBuilder::create();
		self::$htmlBuilder->setOdtDocument($odtDocument);

		self::readDocumentAsHtmlDocument();
		self::parseBody();
		self::parseList();
		self::parseListItem();
		self::readListItemContentAsSpanText();
		self::readTextAsText();
		self::castDrawImageToImage();
		self::castTextAnchorToAnchor();
		self::castTextHeadingToHeading();
		self::castTextSectionToSection();
		self::castTextParagraphToParagraph();


		/*
		 * test
		 */
		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "test"]),
			function(){
				$node = func_get_arg(0);
				/*
				echo "<pre>\n".str_repeat("\t", $node->depth);
				switch($node->tagType)
				{
					case 1:
						echo htmlentities("<".$node->name.">");
						break;
					case 2:
						echo htmlentities("</".$node->name.">");
						break;
					case 3:
						echo htmlentities("<".$node->name."/>");
						break;
				}
				echo "</pre>";
				*/
				/*
				echo "<br/>".
				" <span style=\"color: #00F\">nodeName:</span>".$node->name.
				" <span style=\"color: #00F\">nodeType:</span>".XML_DEFINITION::NODE_TYPE_NAME[$node->type].
				" <span style=\"color: #00F\">tagType:</span>".XML_DEFINITION::TAG_TYPE_NAME[$node->tagType]."_ELEMENT".
				" <span style=\"color: #00F\">path:</span> ".$node->path;
				*/
				}
		);

        self::$parser->parse($content);
    }

	public static function getHtml()
	{
		$htmlDocument = self::$htmlBuilder->getInstance();
		return $htmlDocument->getContent();
	}

    /*
     * Routines and Properties
     */

	protected static function readDocumentAsHtmlDocument()
	{
		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isDocumentStart"]),
			ActionBuilder::create([self::$htmlBuilder, "startDocument"])
		);

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isDocumentEnd"]),
			ActionBuilder::create([self::$htmlBuilder, "endDocument"])
		);
	}

	protected static function parseBody()
	{
		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementBodyOpenTag"]),
			ActionBuilder::create([self::$htmlBuilder, "addElementBodyOpenTag"])
		);

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementBodyCloseTag"]),
			ActionBuilder::create([self::$htmlBuilder, "closeElement"])
		);
	}

	protected static function parseList()
	{
		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementListOpenTag"]),
			ActionBuilder::create([self::$htmlBuilder, "addElementUnorderedListOpenTag"])
		);

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementListCloseTag"]),
			ActionBuilder::create([self::$htmlBuilder, "closeElement"])
		);
	}

	protected static function parseListItem()
	{
		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementListItemOpenTag"]),
			ActionBuilder::create([self::$htmlBuilder, "addElementListItemOpenTag"])
		);

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementListItemCloseTag"]),
			ActionBuilder::create([self::$htmlBuilder, "closeElement"])
		);
	}

	protected static function readListItemContentAsSpanText()
	{
		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementSpanLiOpenTag"]),
			ActionBuilder::create([self::$htmlBuilder, "addElementSpanLiOpenTag"])
		);

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementSpanLiCloseTag"]),
			ActionBuilder::create([self::$htmlBuilder, "closeElement"])
		);
	}

	protected static function readTextAsText()
	{
		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isText"]),
			ActionBuilder::create([self::$htmlBuilder, "addText"])
		);
	}

	protected static function castDrawImageToImage()
	{
		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementImageEmptyTag"]),
			ActionBuilder::create([self::$htmlBuilder, "addElementImageEmptyTag"])
		);
	}

	protected static function castTextAnchorToAnchor()
	{
		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementAnchorOpenTag"]),
			ActionBuilder::create([self::$htmlBuilder, "addElementAnchorOpenTag"])
		);

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementAnchorCloseTag"]),
			ActionBuilder::create([self::$htmlBuilder, "closeElement"])
		);
	}

	protected static function castTextHeadingToHeading()
	{
		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementHeadingOpenTag"]),
			ActionBuilder::create([self::$htmlBuilder, "addElementHeadingOpenTag"])
		);

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementHeadingCloseTag"]),
			ActionBuilder::create([self::$htmlBuilder, "closeElement"])
		);
	}

	protected static function castTextSectionToSection()
	{
		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementSectionOpenTag"]),
			ActionBuilder::create([self::$htmlBuilder, "addElementSectionOpenTag"])
		);

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementSectionCloseTag"]),
			ActionBuilder::create([self::$htmlBuilder, "closeElement"])
		);
	}

	protected static function castTextParagraphToParagraph()
	{
		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementParagraphOpenTag"]),
			ActionBuilder::create([self::$htmlBuilder, "addElementParagraphOpenTag"])
		);

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementParagraphCloseTag"]),
			ActionBuilder::create([self::$htmlBuilder, "closeElement"])
		);
	}

	const NAME = "OdtToXml";
    private static $parser;
    private static $htmlBuilder;
}