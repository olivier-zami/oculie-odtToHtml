<?php
namespace Oculie\Core\Constructor\System;

class File
{
	private $fileName = NULL;
	private $content = NULL;
	
	public function __construct($fileName=NULL)
	{
		if(isset($fileName))
		{
			$this->fileName = $fileName;
		}
		
		if(isset($this->fileName) && file_exists($this->fileName))
		{
			$this->content = file_get_contents($this->fileName);
		}
	}
	
	public function getContent()
	{
		return $this->content;
	}
	
	public function save()
	{
		if(!isset($this->fileName)) throw new \Exception("Nom de fichier indefini.");
		file_put_contents($this->fileName, $this->content);
	}
	
	public function setContent($content)
	{
		$this->content = $content;
	}
}
?>
