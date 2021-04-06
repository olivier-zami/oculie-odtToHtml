<?php
/**
 * Created by PhpStorm.
 * User: ozami
 * Date: 25/02/2020
 * Time: 16:46
 */

namespace Oculie\Core\Implementation;

trait handlerSettingsMethods
{
    public function __call($funcName, $parameters)
    {
        $dump = [
            "call"      => $funcName,
            "Pattern"     => get_class($this->instance),
            "methods"   => get_class_methods($this)
        ];
        if(substr($funcName, 0, 3) == "set")
        {
            if(method_exists($this->instance, $funcName))call_user_func_array([$this->instance, $funcName], $parameters);
            else
            {
                $reflection = new \ReflectionClass(get_class($this->instance));
                $properties = $reflection->getProperties();
                $dump["attribute"] = $properties;
                foreach($properties as $property)
                {
                    if($funcName == "set".ucfirst($property->getName()))
                    {
                        call_user_func_array([$this->instance, $funcName], $parameters);
                        break;
                    }
                }
            }
        }
        elseif(substr($funcName, 0, 3) == "add"){}
        //\Oculie\Debug::dump($this->instance);

        return $this;
    }
}