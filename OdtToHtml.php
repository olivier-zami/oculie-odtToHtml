<?php
namespace Oculie;

use Oculie\OdtToHtml\Builder\Entity\Document\Html as HtmlBuilder;
use Oculie\Core\Definition\Xml as XmlDefinition;
use Oculie\Core\Builder\Callback as ActionBuilder;

class OdtToHtml
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

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isDocumentStart"]),
			ActionBuilder::create([self::$htmlBuilder, "startDocument"])
		);

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isDocumentEnd"]),
			ActionBuilder::create([self::$htmlBuilder, "endDocument"])
		);

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementAnchorOpenTag"]),
			ActionBuilder::create([self::$htmlBuilder, "addElementAnchorOpenTag"])
		);

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementAnchorCloseTag"]),
			ActionBuilder::create([self::$htmlBuilder, "closeElement"])
		);

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementBodyOpenTag"]),
			ActionBuilder::create([self::$htmlBuilder, "addElementBodyOpenTag"])
		);

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementBodyCloseTag"]),
			ActionBuilder::create([self::$htmlBuilder, "closeElement"])
		);

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementHeadingOpenTag"]),
			ActionBuilder::create([self::$htmlBuilder, "addElementHeadingOpenTag"])
		);

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementHeadingCloseTag"]),
			ActionBuilder::create([self::$htmlBuilder, "closeElement"])
		);

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementImageEmptyTag"]),
			ActionBuilder::create([self::$htmlBuilder, "addElementImageEmptyTag"]));


        self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementParagraphOpenTag"]),
			ActionBuilder::create([self::$htmlBuilder, "addElementParagraphOpenTag"]));

		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isElementParagraphCloseTag"]),
			ActionBuilder::create([self::$htmlBuilder, "closeElement"]));


		self::$parser->setEventAction(
			ActionBuilder::create([\Oculie\OdtToHtml\Checker\OpenDocument\Text::class, "isText"]),
			ActionBuilder::create([self::$htmlBuilder, "addText"])
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

    private static $parser;
    private static $htmlBuilder;
}