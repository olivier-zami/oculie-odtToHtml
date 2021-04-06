<?php
namespace Oculie\Utils;

function testToto()
{
    die("TEST TOTO !!!");
}

function testConversion()
{
    {
        $mSchema = [];
        foreach ($model->getFields() as $fName => $field)
        {
            $options = [];
            preg_match("|([a-zA-Z_ ]+)(\(([0-9a-zA-Z_ ]+)\))?|", $field, $match);
            if (strstr($match[1], "unsigned")) {
                $options["unsigned"] = TRUE;
                $type = trim(str_replace("unsigned", "", $match[1]));
            } else $type = trim($match[1]);

            switch ($type) {
                case "char":
                    $mSchema[$fName] = ["type" => "char", "options" => $options];
                    break;
                case "collection":
                    $mSchema[$fName] = ["type" => "collection", "options" => $options];
                    break;
                case "date":
                    $mSchema[$fName] = ["type" => "date", "options" => $options];
                    break;
                case "entity":
                    $options["ref"] = $match[3];
                    $mSchema[$fName] = ["type" => "entity", "options" => $options];
                    break;
                case "float":
                    $mSchema[$fName] = ["type" => "float", "options" => $options];
                    break;
                case "int":
                    $mSchema[$fName] = ["type" => "integer", "options" => $options];
                    break;
                case "string":
                    $mSchema[$fName] = ["type" => "string", "options" => $options];
                    break;
            }
        }
        $modelConfiguration[$mName] = $mSchema;
    }
}
