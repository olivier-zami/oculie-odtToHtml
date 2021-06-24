<?php
namespace Oculie\Core\Parser;

use Oculie\Core\Builder\XmlElement as XmlElementBuilder;
use Oculie\Core\Definition\Xml as XML_DEFINITION;

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

    public function __construct(){}

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

		$node = new \Oculie\Core\Data\Entity\XmlNode();

        while ($xml->read())
        {
            $opened_tags = array();//This array will contain the HTML tags opened in every iteration

            switch($xml->nodeType)
            {
				case \XMLReader::ELEMENT:
					$nAttr = $attr = [];

					$node->name = $xml->name;
					$node->value = $xml->value;
					$node->type = $xml->nodeType;
					$node->depth = $xml->depth;
					$node->tagType = $xml->isEmptyElement ? XML_DEFINITION::TAG_TYPE["AUTO_CLOSE"] : XML_DEFINITION::TAG_TYPE["OPEN"];
					if($xml->hasAttributes)
					{
						while($xml->moveToNextAttribute())
						{
							$attr[$xml->name] = $xml->value;
							$nAttr[] = [
								"name"	=> $xml->name,
								"value"	=> $xml->value,
								"type"	=> $xml->nodeType,
								"depth"	=> $xml->depth,
							];
						}
						$xml->moveToElement();
					}
					$node->attribute = $attr;

					$this->updateNodePath($node, XML_DEFINITION::TOKEN_TYPE["ELEMENT"]);

					$node->parentName = $this->parentName;
					$node->path = $this->currentPath;
					$this->callEventAction($node);

					foreach($nAttr as $attr)
					{
						$node->name = $attr["name"];
						$node->value = $attr["value"];
						$node->type = $attr["type"];
						$node->depth = $attr["depth"];
						$node->tagType = XML_DEFINITION::TAG_TYPE["NONE"];
						$node->attribute = NULL;
						$this->updateNodePath($node, XML_DEFINITION::TOKEN_TYPE["ATTRIBUTE"]);
						$node->parentName = $this->parentName;
						$node->path = $this->currentPath;
						$this->callEventAction($node);
					}


					if($xml->isEmptyElement)
					{
						$this->updateNodePath($node, XML_DEFINITION::TOKEN_TYPE["AUTO_CLOSE_ELEMENT"]);
					}

					break;

				case \XMLReader::ATTRIBUTE:break;

				case \XMLReader::TEXT:
					$node->name = $xml->name;
					$node->value = $xml->value;
					$node->type = $xml->nodeType;
					$node->depth = $xml->depth;
					$node->tagType = XML_DEFINITION::TAG_TYPE["NONE"];
					$node->attribute = NULL;
					$this->updateNodePath($node, XML_DEFINITION::TOKEN_TYPE["TEXT"]);
					$node->parentName = $this->parentName;
					$node->path = $this->currentPath;
					$this->callEventAction($node);
					break;

				case \XMLReader::CDATA:
					$node->name = $xml->name;
					$node->value = \htmlentities($xml->value);
					$node->type = $xml->nodeType;
					$node->depth = $xml->depth;
					$node->tagType = XML_DEFINITION::TAG_TYPE["NONE"];
					$node->attribute = NULL;
					$this->updateNodePath($node, XML_DEFINITION::TOKEN_TYPE["CDATA"]);
					$node->parentName = $this->parentName;
					$node->path = $this->currentPath;
					$this->callEventAction($node);
					break;

				case \XMLReader::PI:
					$node->name = $xml->name;
					$node->value = $xml->value;
					$node->type = $xml->nodeType;
					$node->depth = $xml->depth;
					$node->tagType = XML_DEFINITION::TAG_TYPE["NONE"];
					$node->attribute = NULL;
					$this->updateNodePath($node, XML_DEFINITION::TOKEN_TYPE["PI"]);
					$node->parentName = $this->parentName;
					$node->path = $this->currentPath;
					$this->callEventAction($node);
					break;

				case \XMLReader::COMMENT:
					$node->name = $xml->name;
					$node->value = $xml->value;
					$node->type = $xml->nodeType;
					$node->depth = $xml->depth;
					$node->tagType = XML_DEFINITION::TAG_TYPE["NONE"];
					$node->attribute = NULL;
					$this->updateNodePath($node, XML_DEFINITION::TOKEN_TYPE["COMMENT"]);
					$node->parentName = $this->parentName;
					$node->path = $this->currentPath;
					$this->callEventAction($node);
					break;

				case \XMLReader::DOC_TYPE:
					$node->name = $xml->name;
					$node->value = $xml->value;
					$node->type = $xml->nodeType;
					$node->depth = $xml->depth;
					$node->tagType = XML_DEFINITION::TAG_TYPE["NONE"];
					$node->attribute = NULL;
					$this->updateNodePath($node, XML_DEFINITION::TOKEN_TYPE["DOCTYPE"]);
					$node->parentName = $this->parentName;
					$node->path = $this->currentPath;
					$this->callEventAction($node);
					break;

				case \XMLReader::SIGNIFICANT_WHITESPACE:
					$node->name = $xml->name;
					$node->value = $xml->value;
					$node->type = $xml->nodeType;
					$node->depth = $xml->depth;
					$node->tagType = XML_DEFINITION::TAG_TYPE["NONE"];
					$node->attribute = NULL;
					$this->updateNodePath($node, XML_DEFINITION::TOKEN_TYPE["INDENTATION"]);
					$node->parentName = $this->parentName;
					$node->path = $this->currentPath;
					$this->callEventAction($node);
					break;

				case \XMLReader::END_ELEMENT:
					$node->name = $xml->name;
					$node->value = $xml->value;
					$node->type = $xml->nodeType;
					$node->depth = $xml->depth;
					$node->tagType = XML_DEFINITION::TAG_TYPE["CLOSE"];
					$node->attribute = NULL;
					$this->updateNodePath($node, XML_DEFINITION::TAG_TYPE["CLOSE"]);
					$node->parentName = $this->parentName;
					$node->path = $this->currentPath;
					$this->callEventAction($node);
					break;

				case \XMLReader::ENTITY_REF:
				case \XMLReader::ENTITY:
				case \XMLReader::DOC:
				case \XMLReader::DOC_FRAGMENT:
				case \XMLReader::NOTATION:
				case \XMLReader::WHITESPACE:
				case \XMLReader::END_ENTITY:
				case \XMLReader::XML_DECLARATION:
				default:
					/*
					$node->name = $xml->name;
					$node->value = $xml->value;
					$node->type = $xml->nodeType;
					$node->depth = $xml->depth;
					$node->attribute = NULL;
					$this->updateNodePath($node, XML_DEFINITION::TOKEN_TYPE["UNDEFINED"]);
					$node->parentName = $this->parentName;
					$node->path = $this->currentPath;
					$this->callEventAction($node);
					break;
					*/
			}
            	/*
                case \XMLReader::ELEMENT:
                	$this->elementStack[] = $xml->name;
					$this->currentNode->type = ($xml->isEmptyElement) ? XmlDefinition::EMPTY_TAG : XmlDefinition::OPEN_TAG;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = $xml->value;
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$parentName = $this->generateparentName();
					$this->currentNode->parentName = $parentName;
					$this->callEventAction($this->currentNode);
					/*
					$this->currentNode->type = ($xml->isEmptyElement) ? XmlDefinition::EMPTY_TAG_START : XmlDefinition::OPEN_TAG_START;
					$this->callEventAction($this->currentNode);

					$attribute = [];
					if($xml->hasAttributes)
					{
						while($xml->moveToNextAttribute())
						{
							$attribute[$xml->name] = $xml->value;
							$this->currentNode->type = XmlDefinition::ATTRIBUTE;
							$this->currentNode->name = $xml->name;
							$this->currentNode->value = $xml->value;
							$this->currentNode->depth = $xml->depth;
							$this->currentNode->path = $this->generateXpath($xml);
							$this->currentNode->parentName = $this->generateparentName();
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
					$this->currentNode->parentName = $this->generateparentName();
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
						 *** /
					}
                    break;


                case \XMLReader::TEXT:
					$this->currentNode->type = XmlDefinition::TEXT;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = htmlspecialchars($xml->value);
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$this->currentNode->parentName = $this->generateparentName();
					$this->callEventAction($this->currentNode);
                    break;

                case \XMLReader::CDATA:
					$this->currentNode->type = XmlDefinition::CDATA;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = $xml->value;
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$this->currentNode->parentName = $this->generateparentName();
					$this->callEventAction($this->currentNode);
                	break;

                case \XMLReader::ENTITY_REF:
					$this->currentNode->type = XmlDefinition::ENTITY_REF;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = $xml->value;
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$this->currentNode->parentName = $this->generateparentName();
					$this->callEventAction($this->currentNode);
                	break;

                case \XMLReader::PI:
					$this->currentNode->type = XmlDefinition::PI;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = htmlspecialchars($xml->value);
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$this->currentNode->parentName = $this->generateparentName();
					$this->callEventAction($this->currentNode);
                	break;

                case \XMLReader::COMMENT:
					$this->currentNode->type = XmlDefinition::COMMENT;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = htmlspecialchars($xml->value);
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$this->currentNode->parentName = $this->generateparentName();
					$this->callEventAction($this->currentNode);
                	break;

				case \XMLReader::DOC_TYPE:
					$this->currentNode->type = XmlDefinition::DOCTYPE;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = htmlspecialchars($xml->value);
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$this->currentNode->parentName = $this->generateparentName();
					$this->callEventAction($this->currentNode);
					break;

                case \XMLReader::SIGNIFICANT_WHITESPACE:
					$this->currentNode->type = XmlDefinition::INDENTATION;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = $xml->value;
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$this->currentNode->parentName = $this->generateparentName();
					$this->callEventAction($this->currentNode);
                    break;

                case \XMLReader::END_ELEMENT:

					$this->currentNode->type = XmlDefinition::CLOSE_TAG;
					$this->currentNode->name = $xml->name;
					$this->currentNode->value = NULL;
					$this->currentNode->depth = $xml->depth;
					$this->currentNode->path = $this->generateXpath($xml);
					$this->currentNode->parentName = $this->generateparentName();
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
                    * /
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
                        * /

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
                        }* /

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
            	*/
        }
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
			if(\Oculie\Core\Worker\executionMethods::execute($event))
			{
				$action = $this->action[$trigger[1]];
				$action->setParameters($context);
				\Oculie\Core\Worker\executionMethods::execute($action);
			}
		}
    }

    protected function updateNodePath($node, $tokenType): string
	{
		$nodePathName = "";
		switch($node->type)
		{
			case \XMLReader::ELEMENT:
			case \XMLReader::END_ELEMENT:
				$nodePathName.=$node->name;
				break;
			case \XMLReader::ATTRIBUTE:
				$nodePathName.="@".$node->name;
				break;
			case \XMLReader::TEXT:
				$nodePathName.="text()";
				break;
			case \XMLReader::CDATA:
				$nodePathName.="#CDATA";
				break;
			case \XMLReader::PI:
				$nodePathName.="#PI";
				break;
			case \XMLReader::COMMENT:
				$nodePathName.="#COMMENT";
				break;
			case \XMLReader::DOC_TYPE:
				$nodePathName.="#DOCTYPE";
				break;
			case \XMLReader::SIGNIFICANT_WHITESPACE:
				$nodePathName.="#INDENTATION";
				break;
			case \XMLReader::ENTITY_REF:
			case \XMLReader::ENTITY:
			case \XMLReader::DOC:
			case \XMLReader::DOC_FRAGMENT:
			case \XMLReader::NOTATION:
			case \XMLReader::WHITESPACE:
			case \XMLReader::END_ENTITY:
			case \XMLReader::XML_DECLARATION:
			default:
				$nodePathName.="#undefined";
				break;
		}

		if(!in_array($tokenType, [XML_DEFINITION::TOKEN_TYPE["END_ELEMENT"], XML_DEFINITION::TOKEN_TYPE["AUTO_CLOSE_ELEMENT"]]))
		{
			$this->nodeStack[$node->depth][$nodePathName] = [
				"type"	=> $node->type,
				"name"	=> $node->name,
				"count"	=> isset($this->nodeStack[$node->depth][$nodePathName]["count"]) ? $this->nodeStack[$node->depth][$nodePathName]["count"]+1 : 1
			];
			$this->nodePath[$node->depth] = $nodePathName;
		}

		$parentDepth = $node->depth-1;
		if($parentDepth>=0)
		{
			if(isset($this->nodeStack[$parentDepth]) && isset($this->nodePath[$parentDepth]))
			{
				$this->parentName = $this->nodeStack[$parentDepth][$this->nodePath[$parentDepth]]["name"];
			}
		}
		else $this->parentName = NULL;

		$this->currentPath = "";
		foreach($this->nodePath as $idx=>$name)
		{
			$this->currentPath .= "/".$name.($this->nodeStack[$idx][$name]["type"]==\XMLReader::ELEMENT ? "[".$this->nodeStack[$idx][$name]["count"]."]": "");
		}

		if(!in_array($tokenType, [XML_DEFINITION::TOKEN_TYPE["ELEMENT"]]))
		{
			array_pop($this->nodePath);
			array_pop($this->nodeStack);
		}

		return $this->currentPath;
	}

    protected $strictSyntaxMode = FALSE;
    protected $source			= "";
    protected $trigger  		= [];
    protected $event			= [];
    protected $action			= [];
    protected $nodePath			= [];
    protected $nodeStack 		= [];
    protected $parentName	= NULL;
    protected $currentPath 		= "";
}