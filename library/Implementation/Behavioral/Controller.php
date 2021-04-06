<?php
namespace Oculie;

class Controller extends \Oculie
{
	public static function process($order)
    {
		if(!is_object($order)) return;

		if($order->process["type"] == parent::PROCESS_TYPE_DIRECT_RESOURCE_ACCESS)
		{
			$order->content = file_get_contents($order->process["uri"]);
		}
		else
		{
			self::fillResponseObject($order->body["response"]);

			if(is_string($order->footer["viewer"]) && class_exists($order->footer["viewer"]))
			{
				$order->footer["viewer"] = parent::prepare($order->footer["viewer"]);
			}
			else
			{
				$order->footer["viewer"] = $order->footer["viewer"]->getBuildedInstance();//TODO: ne plus utiliser la methode get BuildedInstance
			}
		}
		parent::push($order);
    }

	protected static function fillResponseObject(&$object)
	{
		$idxObject = 0;
		$responseObject = array();
		$responseObject[] = &$object;
		while(isset($responseObject[$idxObject]))
		{
			if(is_object($responseObject[$idxObject]))
            {
				switch(get_class($responseObject[$idxObject]))
				{
					case "Oculie\\ObjectAccessorDescriptor":
						$responseObject[$idxObject] = parent::getObjectValue($responseObject[$idxObject]);
						//NOTE: integrer dans la pile ?
						break;
					case "stdclass":
						$object_vars = get_object_vars($responseObject[$idxObject]);
		                foreach($object_vars as $varName => $varValue)
		                {
							if(is_object($varValue) && get_parent_class($varValue)=="Oculie\\Entity\\Action")
								$responseObject[$idxObject]->{$varName} = call_user_func_array($varValue->method, $varValue->parameters);
							elseif(is_object($varValue))
								$responseObject[] = &$responseObject[$idxObject]->{$varName};
						}
						break;
					default:
						$class = new \ReflectionClass($responseObject[$idxObject]);
						$properties = $class->getProperties();
						foreach($properties as $idx => $property)
						{
							if(is_array($responseObject[$idxObject]->{$property->name}) || is_object($responseObject[$idxObject]->{$property->name}))
							{
								//TODO: appeler verifier pour Oculie\\Entity\\Action
								$responseObject[] = &$responseObject[$idxObject]->{$property->name};
							}
						}
						break;
				}
            }
            elseif(is_array($responseObject[$idxObject]))
            {
				foreach($responseObject[$idxObject] as $idx => $value)
				{
					$responseObject[] = &$responseObject[$idxObject][$idx];
				}
            }
            else continue;
			$idxObject++;
		}
		//echo "<fieldset><legend></legend><pre>";print_r($object);echo"</pre></fieldset>";
		//die("die @ ".__FILE__." line ".__LINE__);
	}
}
?>
