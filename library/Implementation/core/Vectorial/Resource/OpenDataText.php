<?php
namespace Oculie\OdtToHtml\Data\Resource;

class OpenDataText
{
    /*
     * Behavior
     */

	public function __construct($handler, $uri)
	{
		$this->handler = $handler;
		$this->uri = $uri;
	}

	public function get($content)
	{
		$this->handler->open($this->uri);
		$content = $this->handler->getFromName($content);
		$this->handler->close();
		return $content;
	}

	public function getList($target=NULL)
	{
		$list = [];
		$this->handler->open($this->uri);
		for ($i=0; $i<$this->handler->numFiles; $i++)
		{
			$list[] = $this->handler->getNameIndex($i);
		}
		$this->handler->close();
		return $list;
	}

    /*
     * Routines & Procedures
     */

	protected $handler;
	protected $uri;
}