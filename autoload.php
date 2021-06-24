<?php
spl_autoload_register(function($classname){
	$classMap = [
		\Oculie\OdtToHtml::class								=> __DIR__ . "/OdtToHtml.php",
		\Oculie\Definition\Builder::class						=> __DIR__ . "/library/Definition/Interface/Builder.php",
		\Oculie\Core\Builder::class								=> __DIR__ . "/library/Implementation/core/Creational/Builder.php",
		\Oculie\Core\Builder\Callback::class					=> __DIR__ . "/library/Implementation/core/Creational/Builder/Callback.php",
		\Oculie\Core\Data\Callback::class						=> __DIR__ . "/library/Implementation/core/Vectorial/Callback.php",
		\Oculie\Core\Data\Entity\Callback::class				=> __DIR__ . "/library/Implementation/core/Vectorial/Entity/Callback.php",
		\Oculie\Core\Data\Entity\Document::class				=> __DIR__ . "/library/Implementation/core/Vectorial/Entity/Document.php",
		\Oculie\Core\Data\Entity\XmlNode::class                 => __DIR__ . "/library/Implementation/core/Vectorial/Entity/XmlNode.php",
		\Oculie\Core\Definition\Callback::class					=> __DIR__ . "/library/Definition/Callback.php",
		\Oculie\Core\Definition\Xml::class						=> __DIR__ . "/library/Definition/Xml.php",
		\Oculie\Core\entityPropertiesSettingsMethods::class     => __DIR__ . "/library/Implementation/core/Behavioral/entityPropertiesSettingsMethods.php",
		\Oculie\Core\Extension::class							=> __DIR__ . "/library/Implementation/core/Behavioral/classes/Extension.php",
		\Oculie\Core\Parser\Xml::class                          => __DIR__ . "/library/Implementation/core/Behavioral/classes/Parser/Xml.php",
		\Oculie\Core\Worker::class								=> __DIR__ . "/library/Implementation/core/Behavioral/classes/Worker.php",
		\Oculie\Core\Worker\Callback::class						=> __DIR__ . "/library/Implementation/core/Behavioral/classes/Worker/Callback.php",
		\Oculie\Core\Worker\Closure::class						=> __DIR__ . "/library/Implementation/core/Behavioral/classes/Worker/Closure.php",
		\Oculie\Core\Worker\executionMethods::class				=> __DIR__ . "/library/Implementation/core/Behavioral/traits/worker/executionMethods.php",
		\Oculie\Core\Worker\UserFunctionArray::class			=> __DIR__ . "/library/Implementation/core/Behavioral/classes/Worker/UserFunctionArray.php",
		\Oculie\OdtToHtml\Builder\Entity\Document\Html::class   => __DIR__ . "/library/Implementation/odtToHtml/Creational/Builder/Entity/Document/Html.php",
		\Oculie\OdtToHtml\Checker\OpenDocument\Text::class		=> __DIR__ . "/library/Implementation/odtToHtml/Behavioral/Checker/OpenDocument/Text.php",
		\Oculie\OdtToHtml\Builder\Resource\OpenDataText::class	=> __DIR__ . "/library/Implementation/odtToHtml/Creational/Builder/Resource/OpenDataText.php",
		\Oculie\OdtToHtml\Data\Resource\OpenDataText::class		=> __DIR__ . "/library/Implementation/core/Vectorial/Resource/OpenDataText.php",
	];
	if(!isset($classMap[$classname])) throw new \Exception("Class \"".$classname."\" can not be found. Try command \"composer update\"");
	include $classMap[$classname];
});