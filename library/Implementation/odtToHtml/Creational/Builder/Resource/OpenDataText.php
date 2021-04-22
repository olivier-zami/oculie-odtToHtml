<?php
namespace Oculie\OdtToHtml\Builder\Resource;

class OpenDataText /*extends Oculie\Core\Builder\Folder*/
{
    /*
     * Interface
     */

    public static function create(): OpenDataText
    {
        if(!isset(self::$builder))self::$builder = new OpenDataText();
        //if(!isset(self::$zipUtility)) self::initZipUtility();
        return self::$builder;
    }

    public function setUri($uri): OpenDataText
    {
        if(!file_exists($uri))throw new \Exception("File \"".$uri."\" cannot be found.");
        $this->uri = $uri;
        $this->handler = new \ZipArchive;//TODO: check for \ZipArchive existence
        if($this->handler->open($uri)===FALSE)throw new \Exception("ODT file \"".$uri."\" cannot be opened.");
		$this->handler->close();
        return $this;
    }

    public function getInstance(): \Oculie\OdtToHtml\Data\Resource\OpenDataText
    {
        $instance = new \Oculie\OdtToHtml\Data\Resource\OpenDataText($this->handler, $this->uri);
        $this->uri = NULL;
        $this->handler = NULL;
        return $instance;
    }

    /*
    * Routines & Properties
    */

    private static function initZipUtility()
    {
        if(class_exists("ZipArchive"))
        {
            self::$zipUtility = "ZipArchive";
        }
        else throw new \Exception("Zip file utility must be installed on you PHP environment.");
    }

    private static function readWithZipArchive(){}

    protected static $builder;
    private static $zipUtility;

    private $uri;
    private $handler;
}