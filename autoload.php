<?php
spl_autoload_register(function($classname){
	$classMap = [
		\Oculie\OdtToHtml::class								=> __DIR__ . "/OdtToHtml.php",
		\Oculie\Core\Builder\Callback::class					=> __DIR__ . "/library/Implementation/core/Creational/Builder/Callback.php",
		\Oculie\Core\Parser\Xml::class                          => __DIR__ . "/library/Implementation/core/Behavioral/Parser/Xml.php",
		\Oculie\Core\Data\Entity\XmlNode::class                 => __DIR__ . "/library/Implementation/core/Vectorial/Entity/XmlNode.php",
		\Oculie\Core\entityPropertiesSettingsMethods::class     => __DIR__ . "/library/Implementation/core/Behavioral/entityPropertiesSettingsMethods.php",
		\Oculie\OdtToHtml\Builder\Entity\Document\Html::class   => __DIR__ . "/library/Implementation/odtToHtml/Creational/Builder/Entity/Document/Html.php",
		\Oculie\OdtToHtml\Checker\OpenDocument\Text::class		=> __DIR__ . "/library/Implementation/odtToHtml/Behavioral/Checker/OpenDocument/Text.php",
		\Oculie\Core\Definition\Callback::class					=> __DIR__ . "/library/Definition/Callback.php",
		\Oculie\Core\Callback::class							=> __DIR__ . "/library/Implementation/core/Vectorial/Callback.php",
		\Oculie\Core\Definition\Model\Callback::class			=> __DIR__ . "/library/Definition/Model/Callback.php",
		\Oculie\OdtToHtml\Builder\Resource\OpenDataText::class	=> __DIR__ . "/library/Implementation/odtToHtml/Creational/Builder/Resource/OpenDataText.php",
		\Oculie\OdtToHtml\Data\Resource\OpenDataText::class		=> __DIR__ . "/library/Implementation/core/Vectorial/Resource/OpenDataText.php",
		\Oculie\Core\Definition\Xml::class						=> __DIR__ . "/library/Definition/Xml.php",
		\Oculie\Core\Data\Entity\Document::class				=> __DIR__ . "/library/Implementation/core/Vectorial/Entity/Document.php"
	];
	if(!isset($classMap[$classname])) throw new \Exception("Class \"".$classname."\" can not be found. Try command \"composer update\"");
	include $classMap[$classname];
});