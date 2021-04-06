<?php
namespace Oculie;

Trait PropertyAccessTrait
{
    public function &__get($attributesName)
    {
        if(!property_exists($this, $attributesName))throw new Exception("Attribut \"".$attributesName."\"inexistant. Le DTO \"".get_class($this)."\" ne contient pas d'attribut \"".$attributesName."\"");
        return $this->{$attributesName};
    }

    public function __set($attributesName, $value)
    {
        if(!property_exists($this, $attributesName))throw new Exception("Attribut \"".$attributesName."\"inexistant. Le DTO \"".get_class($this)."\" ne contient pas d'attribut \"".$attributesName."\"");

        if(method_exists($this, $method="set".strtoupper(substr($attributesName, 0, 1)).substr($attributesName, 1)))
        {
            $this->{$method}($value);
        }
        else $this->{$attributesName} = $value;
    }
}
?>
