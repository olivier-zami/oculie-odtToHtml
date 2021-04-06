<?php
namespace Oculie;

Trait ChainedSetterTrait
{
	public function __call($methodName, $parameters)
    {
        if(strtolower(substr($methodName, 0, 3))!="set") throw new Exception("Methode chainée \"".$methodName."\" invalide");
        $attributeName = substr($methodName, 3);
        $attributeName = strtolower($attributeName[0]).substr($attributeName, 1);
        $this->{$attributeName} = $parameters[0];
        return $this;
    }

	public function __get($varName)
	{
		if(!property_exists($this, $varName)) throw new \Exception("Tentative d'accès à une propriété inexistante");
		return $this->{$varName};
	}

	public function __set($varName, $value)
	{
		if(!property_exists($className, $varName)) throw new \Exception("Tentative d'accès à une propriété inexistante.");
		$this->{$varName} = $value;
	}
}
?>
