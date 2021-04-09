<?php
namespace Oculie;

use Oculie\Core\Definition\Xml as XmlDefinition;

class OdtToXml
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
            $content = $odtDocument->getContent();
        }

        if(!isset(self::$parser))self::$parser = new \Oculie\Core\Parser\Xml();


        $visitor = new class(){public $html = "";};

		self::$parser->setVisitor($visitor);

		self::$parser->setEventAction(function(){
			$node = func_get_arg(0);
			return $node->type==XmlDefinition::ELEMENT&&$node->name=="office:document-content"&&$node->tagType==XmlDefinition::OPENING_TAG;
		},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.="<html>\n";
			});

		self::$parser->setEventAction(function(){
			$node = func_get_arg(0);
			return $node->type==XmlDefinition::ELEMENT&&$node->name=="office:document-content"&&$node->tagType==XmlDefinition::CLOSING_TAG;
		},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.="</html>";
			});

        self::$parser->setEventAction(function(){
        		$node = func_get_arg(0);
        		return $node->type==XmlDefinition::ELEMENT&&$node->name=="text:p"&&$node->tagType==XmlDefinition::OPENING_TAG;
        	},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.="<p>";
        });

		self::$parser->setEventAction(function(){
			$node = func_get_arg(0);
			return $node->type==XmlDefinition::ELEMENT&&$node->name=="text:p"&&$node->tagType==XmlDefinition::CLOSING_TAG;
		},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.="</p>\n";
			});

		self::$parser->setEventAction(function(){
			$node = func_get_arg(0);
			return $node->type==XmlDefinition::TEXT;
		},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.=$node->value;
			});

        self::$parser->parse($content);
        return self::$parser->getVisitor()->html;
    }

    /*
     * Routines and Properties
     */

    private static $parser;
}