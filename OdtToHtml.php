<?php
namespace Oculie;

use Oculie\Core\Definition\Xml as XmlDefinition;

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

        if(!isset(self::$parser))self::$parser = new \Oculie\Core\Parser\Xml();


        $visitor = new class($odtDocument){public $html = ""; public $doc=[];
        	public function __construct($doc){
        		foreach($doc->getList() as $file)
				{
					if(strstr($file,"Pictures"))
					{
						$this->doc[$file] = $doc->get($file);
					}
				}
			}};

		self::$parser->setVisitor($visitor);

		self::$parser->setEventAction(
			function(){
				$node = func_get_arg(0);
				return $node->type==XmlDefinition::OPEN_TAG&&$node->name=="office:document-content";
			},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.="<html>\n\t<head>";
			}
		);

		self::$parser->setEventAction(
			function()
			{
				$node = func_get_arg(0);
				return $node->type==XmlDefinition::CLOSE_TAG&&$node->name=="office:document-content";
			},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.="\n</html>";
			}
		);

		self::$parser->setEventAction(
			function()
			{
				$node = func_get_arg(0);
				return $node->type==XmlDefinition::OPEN_TAG&&$node->name=="office:body";
			},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.="\n\t</head>\n\t<body>";
			}
		);

		self::$parser->setEventAction(
			function()
			{
				$node = func_get_arg(0);
				return $node->type==XmlDefinition::CLOSE_TAG&&$node->name=="office:body";
			},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.="\n\t</body>";
			}
		);

		self::$parser->setEventAction(
			function()
			{
				$node = func_get_arg(0);
				return $node->type==XmlDefinition::OPEN_TAG_START&&$node->name=="text:h";
			},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.="\n\t<h";
			}
		);

		self::$parser->setEventAction(
			function()
			{
				$node = func_get_arg(0);
				return $node->type==XmlDefinition::OPEN_TAG_END&&$node->name=="text:h";
			},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.= (isset($node->attribute["text:outline-level"])?($node->attribute["text:outline-level"]>6?6:$node->attribute["text:outline-level"]):"").">";
			}
		);

		self::$parser->setEventAction(
			function()
			{
				$node = func_get_arg(0);
				return $node->type==XmlDefinition::CLOSE_TAG&&$node->name=="text:h";
			},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.="</h".(isset($node->attribute["text:outline-level"])?($node->attribute["text:outline-level"]>6?6:$node->attribute["text:outline-level"]):"").">";
			}
		);

		/*
		 * tag : a
		 */

		self::$parser->setEventAction(
			function()
			{
				$node = func_get_arg(0);
				return $node->type==XmlDefinition::OPEN_TAG_START&&$node->name=="text:a";
			},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.="\n\t<a";
			}
		);

		self::$parser->setEventAction(
			function()
			{
				$node = func_get_arg(0);
				return $node->type==XmlDefinition::OPEN_TAG_END&&$node->name=="text:a";
			},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.= " ".(isset($node->attribute["xlink:href"])?"href=\"".$node->attribute["xlink:href"]."\"":"").">";
			}
		);

		self::$parser->setEventAction(
			function()
			{
				$node = func_get_arg(0);
				return $node->type==XmlDefinition::CLOSE_TAG&&$node->name=="text:a";
			},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.= "</a>";
			}
		);

		/*
		 * tag : img
		 */

		self::$parser->setEventAction(
			function()
			{
				$node = func_get_arg(0);
				return $node->type==XmlDefinition::EMPTY_TAG_START&&$node->name=="draw:image";
			},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.="\n\t<img";
			}
		);

		self::$parser->setEventAction(
			function()
			{
				$node = func_get_arg(0);
				return $node->type==XmlDefinition::EMPTY_TAG_END&&$node->name=="draw:image";
			},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$docPath = "";
				if(strstr($node->attribute["xlink:href"], "ObjectReplacements"))//TODO: find a way to handle ObjectReplacements file (VCLMTF format)
				{
					$src = "";
				}
				else
				{
					$src = 'data:image;base64,'.base64_encode($v->doc[$node->attribute["xlink:href"]]);
				}
				$v->html.=(isset($node->attribute["xlink:href"])?" src=\"".$src."\"":"")."/>";
			}
		);

		/*
		 * tag : p
		 */

        self::$parser->setEventAction(
        	function(){
        		$node = func_get_arg(0);
        		return $node->type==XmlDefinition::OPEN_TAG&&$node->name=="text:p";
        	},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.= "\n".str_repeat("\t", $node->depth-1)."<p>";
        });

		self::$parser->setEventAction(
			function(){
				$node = func_get_arg(0);
				return $node->type==XmlDefinition::CLOSE_TAG&&$node->name=="text:p";
			},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.="</p>";
			});

		self::$parser->setEventAction(
			function(){
				$node = func_get_arg(0);
				return $node->type==XmlDefinition::TEXT;
			},
			function(){
				$node = func_get_arg(0);
				$v = func_get_arg(1);
				$v->html.=$node->value;
			});

        self::$parser->parse($content);
    }
    public static function getHtml()
	{
		return self::$parser->getVisitor()->html;
	}

    /*
     * Routines and Properties
     */

    private static $parser;
}