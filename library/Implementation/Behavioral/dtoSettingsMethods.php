<?php
/**
 * Created by PhpStorm.
 * User: ozami
 * Date: 01/03/2020
 * Time: 19:31
 */

namespace Oculie\Core\Implementation;

trait dtoSettingsMethods
{
    public function __call($funcName, $parameters)
    {
        $method_exists = FALSE;

        foreach(get_class_vars(get_class($this)) as $varName=>$varValue)
        {
            if($funcName=="set".ucfirst($varName) || $funcName=="get".ucfirst($varName))
            {
                $method_exists = TRUE;
                break;
            }
        }

        if(!$method_exists)throw new \Exception("methode \"".$funcName."\" inexistante");
        else
        {
            $returnValue = NULL;
            if(substr($funcName,0, 3)=="set")
            {
                $this->{lcfirst(substr($funcName, 3))} = $parameters[0];
            }
            elseif(substr($funcName,0, 3)=="get")
            {
                $returnValue = $this->{lcfirst(substr($funcName, 3))};
            }
        }
        return $returnValue;
    }
}