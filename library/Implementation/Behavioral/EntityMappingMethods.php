<?php
trait EntityMappingMethods
{
    private $_propertyMapping = array();

    public function __call($method, $parameters)
    {
        $attributesName = substr($method, strlen("set"));
        $attributesName = strtolower(substr($attributesName, 0, 1)).substr($attributesName, 1);
        $this->_propertyMapping[$attributesName] = $parameters;
        return $this;
    }

	public function getProperties()
	{
		$propertiesName = array();
		foreach(get_object_vars($this) as $propery => $dataCallBack)
		{
			if(in_array($propery, array("_propertyMapping"))) continue;
			$propertiesName[] = $propery;
		}
		return $propertiesName;
	}

	public function getPropertyModel($propertyName)
	{
		return isset($this->_propertyMapping[$propertyName][0]) ? $this->_propertyMapping[$propertyName][0] : NULL;		
	}

	public function getPropertyValue($propertyName)
	{
		if(!isset($this->_propertyMapping[$propertyName])) return NULL;
		$parameters = $this->_propertyMapping[$propertyName];
		$className = array_shift($parameters);
		$methodsName = array_shift($parameters);
		return call_user_func_array(array($className, $methodsName), $parameters);
	}
}
?>
