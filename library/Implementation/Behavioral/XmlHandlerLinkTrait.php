<?php
Trait XmlHandlerLinkTrait
{
	public function addLinkStyleSheet($url)
	{
		$this->select("/html/head")->insert("<link rel=\"stylesheet\" type=\"text/css\" href=\"".$url."\"/>");
	}
}
?>
