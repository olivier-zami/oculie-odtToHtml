<?php
namespace Oculie\Core\Accessor;

class File extends \Oculie\Core\Dto\File
{
	private $instance;
	
	public function get($instance)
	{
		//TODO: verifier si la classe est de type Dto\File1
		$this->instance = $instance;
		return $this;
	}
	
	public function setContent($content)
	{
		$this->instance->content = $content;
		return $this;
	}
	
	public function getContent()
	{
		return $this->instance->content;
	}
}
?>