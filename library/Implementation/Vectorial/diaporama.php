<?php
class Diaporama
{
	private $source;
	
	public function setData($data)
	{
		$this->source = $data;
	}
	
	public function getSource()
	{
		return $this->source;
	}
}
?>