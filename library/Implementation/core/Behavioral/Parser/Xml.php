<?php
namespace Oculie\Core\Parser;

use Oculie\Core\Builder\XmlElement as XmlElementBuilder;
use Oculie\Core\Definition\Xml as XmlDefinition;

include __DIR__ . "/../../../../Action/execute.php";

class Xml
{
    public static $_ophir_odt_import_conf = array
    (
        "features" => array
        (
            "header" => 2,
            "list" => 2,
            "table" => 2,
            "footnote" => 2,
            "link" => 2,
            "image" => 2,
            "note" => 2,
            "annotation" => 2,
            'table of contents' => 0,
        ),
        "images_folder" => "images"
    );

    public function __construct()
	{
		$this->currentNode = new \Oculie\Core\Data\Entity\XmlNode();
	}

    public function setEventAction($event, $action)
	{
		//TODO: check parameters validity
		if(!is_object($event)||get_class($event)=="Closure")
		{
			if(!is_callable($event))throw new \Exception("First parmeters of ".__METHOD__." must be callable");
			$event = \Oculie\Core\Builder\Callback::create()
				->setMethod($event)
				->setParameters()
				->getInstance();
		}
		$this->event[] = $event;
		end($this->event);
		$idxLastEvent = key($this->event);

		if(!is_object($action)||get_class($action)=="Closure")
		{
			if(!is_callable($action))throw new \Exception("Second parameters of ".__METHOD__." must be callable");
			$action = \Oculie\Core\Builder\Callback::create()
				->setMethod($action)
				->setParameters()
				->getInstance();
		}
		$this->action[] = $action;
		end($this->action);
		$idxLastAction = key($this->action);

		$this->trigger[] = [$idxLastEvent, $idxLastAction];
	}

    public function parse($source)
    {
    	$this->source = $source;

        $xml = new \XMLReader();
        if($xml->xml($source)===FALSE)
        {
            throw new \Exception("Invalid file contents.");
        }

        /**************************************************************************************************************/
        // Export the configuration variable that will be overridden by library users
        $OPHIR_CONF = self::$_ophir_odt_import_conf;



        static $styles = array("Quotations" => array("tags" => array("blockquote")));



        $translation_table = array ();
        $translation_table['draw:frame'] = 'div class="odt-frame"';
        if (self::$_ophir_odt_import_conf["features"]["list"]===0) $translation_table["text:list"] = FALSE;
        elseif (self::$_ophir_odt_import_conf["features"]["list"]===2) {
            $translation_table["text:list"] = "ul";
            $translation_table["text:list-item"] = "li";
        }
        if (self::$_ophir_odt_import_conf["features"]["table"]===0) $translation_table["table:table"] = FALSE;
        elseif (self::$_ophir_odt_import_conf["features"]["table"]===2) {
            $translation_table["table:table"] = "table cellspacing=0 cellpadding=0 border=1";
            $translation_table["table:table-row"] = "tr";
            $translation_table["table:table-cell"] = "td";
        }
        if (self::$_ophir_odt_import_conf["features"]["table of contents"]===0) $translation_table['text:table-of-content'] = FALSE;
        elseif (self::$_ophir_odt_import_conf["features"]["table of contents"]===2) {
            $translation_table['text:table-of-content'] = 'div class="odt-table-of-contents"';
        }
        $translation_table['text:line-break'] = 'br';
        /**************************************************************************************************************/

        $html = "";
        $footnotes = "";

		$xml->setParserProperty(\XMLReader::LOADDTD, FALSE);
		$xml->setParserProperty(\XMLReader::DEFAULTATTRS, FALSE);
		$xml->setParserProperty(\XMLReader::VALIDATE, FALSE);
		$xml->setParserProperty(\XMLReader::SUBST_ENTITIES, FALSE);

		$nodeStack = [];
		$elementCount = [[]];

        while ($xml->read())
        {
            $opened_tags = array();//This array will contain the HTML tags opened in every iteration

            switch($xml->nodeType)
            {
                case \XMLReader::ELEMENT:
                	$this->elementStack[] = $xml->name;
					$this->currentNode->type = ($xml->isEmptyElement) ? XmlDefinition::EMPTY_TAG : XmlDefinition::OPEN_TAG;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = $xml->value;
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$this->callEventAction($this->currentNode);
					$this->currentNode->type = ($xml->isEmptyElement) ? XmlDefinition::EMPTY_TAG_START : XmlDefinition::OPEN_TAG_START;
					$this->callEventAction($this->currentNode);

					$attribute = [];
					if($xml->hasAttributes)
					{
						$this->currentNode->parentName = $this->currentNode->name;
						while($xml->moveToNextAttribute())
						{
							$attribute[$xml->name] = $xml->value;
							$this->currentNode->type = XmlDefinition::ATTRIBUTE;
							$this->currentNode->name = $xml->name;
							$this->currentNode->value = $xml->value;
							$this->currentNode->depth = $xml->depth;
							$this->currentNode->path = $this->generateXpath($xml);
							$this->callEventAction($this->currentNode);
						}
						$xml->moveToElement();
					}

					$this->currentNode->attribute = $attribute;
					$this->currentNode->type = ($xml->isEmptyElement) ? XmlDefinition::EMPTY_TAG_END : XmlDefinition::OPEN_TAG_END;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = $xml->value;
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$this->callEventAction($this->currentNode);

                    if($xml->isEmptyElement)
                    {
                    	array_pop($this->nodeStack);
                    }
                    else
					{
						/**
						$current_element_style = $xml->getAttribute("text:style-name");
						if ($current_element_style && isset($styles[$current_element_style]))
						{
						//Styling tags management
						foreach ($styles[$current_element_style]["tags"] as $HTML_tag)
						{
						$html .= "<" . $HTML_tag . ">";
						$opened_tags[] = $HTML_tag;
						}
						}
						 ***/
					}
                    break;


                case \XMLReader::TEXT:
					$this->currentNode->type = XmlDefinition::TEXT;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = htmlspecialchars($xml->value);
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$this->callEventAction($this->currentNode);
                    break;

                case \XMLReader::CDATA:
					$this->currentNode->type = XmlDefinition::CDATA;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = $xml->value;
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$this->callEventAction($this->currentNode);
                	break;

                case \XMLReader::ENTITY_REF:
					$this->currentNode->type = XmlDefinition::ENTITY_REF;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = $xml->value;
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$this->callEventAction($this->currentNode);
                	break;

                case \XMLReader::PI:
					$this->currentNode->type = XmlDefinition::PI;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = htmlspecialchars($xml->value);
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$this->callEventAction($this->currentNode);
                	break;

                case \XMLReader::COMMENT:
					$this->currentNode->type = XmlDefinition::COMMENT;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = htmlspecialchars($xml->value);
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$this->callEventAction($this->currentNode);
                	break;

				case \XMLReader::DOC_TYPE:
					$this->currentNode->type = XmlDefinition::DOCTYPE;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = htmlspecialchars($xml->value);
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$this->callEventAction($this->currentNode);
					break;

                case \XMLReader::SIGNIFICANT_WHITESPACE:
					$this->currentNode->type = XmlDefinition::INDENTATION;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = $xml->value;
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$this->callEventAction($this->currentNode);
                    break;

                case \XMLReader::END_ELEMENT:

					$this->currentNode->type = XmlDefinition::CLOSE_TAG;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = NULL;
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
                    $this->callEventAction($this->currentNode);
					array_pop($this->nodeStack);

                    /*
                    do
                    {
                        if ($element && $element["tags"])
                        {
                            //Close opened tags
                            $element["tags"] = array_reverse($element["tags"]);
                            foreach ($element["tags"] as $HTML_tag) {
                                //$html.= "<font style='color:red' title='Closing $HTML_tag, from $element[name]. Current element is " .($xml->name). "'>©</font>";
                                $HTML_tag = current(explode(" ", $HTML_tag));
                                $html .= "</" . $HTML_tag . ">";
                            }
                        }
                    } while ($xml->name !== $element["name"] && $element); //Close every opened tags. This should also handle malformed Text files

                    continue 2;
                    */
                    break;

				case \XMLReader::NONE:
				case \XMLReader::ATTRIBUTE:
				case \XMLReader::ENTITY:
				case \XMLReader::DOC:
				case \XMLReader::DOC_TYPE:
				case \XMLReader::DOC_FRAGMENT:
				case \XMLReader::NOTATION:
				case \XMLReader::WHITESPACE:
				case \XMLReader::END_ENTITY:
				case \XMLReader::XML_DECLARATION:
					$node = [
						\XMLReader::NONE			=> "NODE",
						\XMLReader::ATTRIBUTE		=> "ATTRIBUTE",
						\XMLReader::ENTITY			=> "ENTITY",
						\XMLReader::DOC				=> "DOCUMENT",
						\XMLReader::DOC_FRAGMENT	=> "DOCUMENT_FRAGMENT",
						\XMLReader::NOTATION		=> "NOTATION",
						\XMLReader::END_ENTITY		=> "END_ENTITY",
						\XMLReader::XML_DECLARATION	=> "XML_DECLARATION"
					];
					throw new \Exception("XmlNode type \"".$node[$xml->nodeType]."\" is not managed");
                    break;
            }

            if (in_array($xml->nodeType,
                array(
                    \XMLReader::ELEMENT,
                    \XMLReader::TEXT,
                    \XMLReader::SIGNIFICANT_WHITESPACE)
            ))
            {
                switch ($xml->name)
				{
                    case "text:p"://Paragraph
                        //Just convert odf <text:p> to html <p>
                        $tags = @$styles[$xml->getAttribute("text:style-name")]["tags"];
                        if (!($tags && !in_array("blockquote", $tags))) {
                            // Do not print a <p> immediatly after or before a <blockquote>
                            $opened_tags[] = "p";
                            $html .= "\n<p>";
                        }
                        break;

                    case "text:tab":
                        $html .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                        break;
/*
                    case "draw:image":
                        if (self::$_ophir_odt_import_conf["features"]["image"]===0) {
                            $xml->next();
                            break;
                        }
                        elseif (self::$_ophir_odt_import_conf["features"]["image"]===1) break;

                        $image_file = 'zip://' . $odtDocument . '#' . $xml->getAttribute("xlink:href");
                        if (isset(self::$_ophir_odt_import_conf["images_folder"]) &&
                            is_dir(self::$_ophir_odt_import_conf["images_folder"]) ) {
                            if (self::ophir_is_image($image_file)) {
                                $image_to_save = self::$_ophir_odt_import_conf["images_folder"] . '/' . basename($image_file);
                                if ( !($src = self::ophir_copy_file ($image_file, $image_to_save))) {
                                    self::ophir_error("Unable to move image file");
                                    break;
                                }
                            } else {
                                self::ophir_error("Found invalid image file.");
                                break;
                            }
                        }
                        else {
                            //self::ophir_error('Unable to save the image. Creating a data URL. Image saved directly in the body.F');
                            $src = 'data:image;base64,' . base64_encode(file_get_contents($image_file));
                        }
                        $html .= "\n<img src=\"$src\" />";
                        break;
                        */

                    case "style:style":
                        $name = $xml->getAttribute("style:name");
                        $parent = $xml->getAttribute("style:parent-style-name");
                        if (array_key_exists($parent, $styles)) $styles[$name] = $styles[$parent]; //Not optimal

                        if ($xml->isEmptyElement) break; //We can't handle that at the moment
                        while ( $xml->read() && //Read one tag
                            ($xml->name != "style:style" || $xml->nodeType != \XMLReader::END_ELEMENT) //Stop on </style:style>
                        ) {
                            if ($xml->name == "style:text-properties") {
                                if ($xml->getAttribute("fo:font-style") == "italic")
                                    $styles[$name]["tags"][] = "em"; //Creates the style and add <em> to its tags

                                if ($xml->getAttribute("fo:font-weight") == "bold")
                                    $styles[$name]["tags"][] = "strong"; //Creates the style and add <strong> to its tags

                                if ($xml->getAttribute("style:text-underline-style") == "solid")
                                    $styles[$name]["tags"][] = "u"; //Creates the style and add <u> to its tags

                            }
                        }
                        break;
                        /*
                    case "text:note":
                        if (self::$_ophir_odt_import_conf["features"]["note"]===0) {
                            $xml->next();
                            break;
                        }
                        elseif (self::$_ophir_odt_import_conf["features"]["note"]===1) break;
                        $note_id = $xml->getAttribute("text:id");
                        $note_name = "Note";
                        while ( $xml->read() && //Read one tag
                            ($xml->name != "text:note" || $xml->nodeType != \XMLReader::END_ELEMENT) //Stop on </style:style>
                        ) {
                            if ($xml->name=="text:note-citation" &&
                                $xml->nodeType == \XMLReader::ELEMENT)
                                $note_name = $xml->readString();
                            elseif ($xml->name=="text:note-body" &&
                                $xml->nodeType == \XMLReader::ELEMENT) {
                                $note_content = odt2html($odtDocument, $xml->readOuterXML());
                            }
                        }*/

                        $html .= "<sup><a href=\"#odt-footnote-$note_id\" class=\"odt-footnote-anchor\" name=\"anchor-odt-$note_id\">$note_name</a></sup>";

                        $footnotes .= "\n" . '<div class="odt-footnote" id="odt-footnote-' . $note_id . '" >';
                        $footnotes .= '<a class="footnote-name" href="#anchor-odt-' . $note_id . '">' . $note_name . ' .</a> ';
                        $footnotes .= $note_content;
                        $footnotes .= '</div>' . "\n";
                        break;

                    case "office:annotation":
                        if (self::$_ophir_odt_import_conf["features"]["annotation"]===0) {
                            $xml->next();
                            break;
                        }
                        elseif (self::$_ophir_odt_import_conf["features"]["annotation"]===1) break;
                        $annotation_id = (isset($annotation_id))?$annotation_id+1:1;
                        $annotation_content = "";
                        $annotation_creator = "Anonymous";
                        $annotation_date = "";
                        do{
                            $xml->read();
                            if ($xml->name=="dc:creator" &&
                                $xml->nodeType == \XMLReader::ELEMENT)
                                $annotation_creator = $xml->readString();
                            elseif ($xml->name=="dc:date" &&
                                $xml->nodeType == \XMLReader::ELEMENT) {
                                $annotation_date = date("jS \of F Y, H\h i\m", strtotime($xml->readString()));
                            }
                            elseif ($xml->nodeType == \XMLReader::ELEMENT) {
                                $annotation_content .= $xml->readString();
                                $xml->next();
                            }
                        }while (!($xml->name === "office:annotation" &&
                            $xml->nodeType === \XMLReader::END_ELEMENT));//End of the note

                        $html .= '<sup><a href="#odt-annotation-' . $annotation_id . '" name="anchor-odt-annotation-' . $annotation_id . '" title="Annotation (' . $annotation_creator . ')">(' . $annotation_id . ')</a></sup>';
                        $footnotes .= "\n" . '<div class="odt-annotation" id="odt-annotation-' . $annotation_id . '" >';
                        $footnotes .= '<a class="annotation-name" href="#anchor-odt-annotation-' . $annotation_id . '"> (' . $annotation_id . ')&nbsp;</a>';
                        $footnotes .= "\n" . '<b>' . $annotation_creator . ' (<i>' . $annotation_date . '</i>)</b> :';
                        $footnotes .= "\n" . '<div class="odt-annotation-content">' . $annotation_content . '</div>';
                        $footnotes .= '</div>' . "\n";
                        break;

                    default:
                        if (array_key_exists($xml->name, $translation_table)) {
                            if ($translation_table[$xml->name]===FALSE) {
                                $xml->next();
                                break;
                            }
                            $tag = explode(" ", $translation_table[$xml->name], 1);
                            //$tag[0] is the tag name, other indexes are attributes
                            $opened_tags[] = $tag[0];
                            $html .= "\n<" . $translation_table[$xml->name] . ">";
                        }
                }
            }
        }

        echo "\n\n\n";
        //return $html . $footnotes;
    }
    /*
     * Routines & Properties
     */

    protected function callEventAction($context)
    {
    	foreach($this->trigger as $trigger)
		{
			$event = $this->event[$trigger[0]];
			$event->setParameters($context);
			if(\Oculie\execute($event))
			{
				$action = $this->action[$trigger[1]];
				$action->setParameters($context);
				\Oculie\execute($action);
			}
		}
    }

    protected function generateXpath($xml): string
	{
		if(!isset($this->nodeStack[$xml->depth]))
		{
			$this->nodeStack[] = [
				"type"	=> $xml->nodeType,
				"name"	=> $xml->name,
				"count"	=> [$xml->name => 1]
			];
		}
		else
		{
			$this->nodeStack[$xml->depth]["name"] = $xml->name;
			$this->nodeStack[$xml->depth]["type"] = $xml->nodeType;
			if(!isset($this->nodeStack[$xml->depth]["count"][$xml->name]))
			{
				$this->nodeStack[$xml->depth]["count"][$xml->name] = 1;
			}
			else
			{
				if($this->nodeStack[$xml->depth]["type"]==\XMLReader::END_ELEMENT)
					$this->nodeStack[$xml->depth]["count"][$xml->name]++;
			}
		}

		$path = "";
		for($i=0; $i<count($this->nodeStack);$i++)
		{
			switch($this->nodeStack[$i]["type"])
			{
				case \XMLReader::ELEMENT:
					$path.="/".$this->nodeStack[$i]["name"]."[".$this->nodeStack[$i]["count"][$this->nodeStack[$i]["name"]]."]";
					break;
				case \XMLReader::ATTRIBUTE:
					$path.="[@".$xml->name."]";
					break;
				case \XMLReader::END_ELEMENT:
					$path.= "";
					break;
				default:
					$path.=$xml->name;
					break;

			}
		}
		return $path;
	}

    protected $strictSyntaxMode = FALSE;
    protected $source		= "";
    protected $trigger  	= [];
    protected $event		= [];
    protected $action		= [];
	protected $currentNode;
	protected $elementStack	= [];
    protected $nodeStack 	= [];
}