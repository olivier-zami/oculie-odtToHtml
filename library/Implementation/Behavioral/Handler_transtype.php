<?php

namespace Oculie\Core;

class Handler_transtype
{
    use inquireMethods;

    protected $instance;

    public function handle($instance)
    {
        $this->instance = $instance;
    }

    public function getInstance()
    {
        return $this->instance;
    }

    public function getInstanceAsArray()//TODO: la classe doit être un dto
    {
        $response = [];
        $stack = [];
        $stack[] = ["r"=>[], "i"=>$this->instance];
        for($i=0;isset($stack[$i]);$i++)
        {
            $reflection = new \ReflectionClass(get_class($stack[$i]["i"]));
            $props = $reflection->getProperties();
            if(is_subclass_of($stack[$i]["i"], Entity\Dynamic::class))
            {
                $props = $reflection->getProperty("content");
                $props->setAccessible(TRUE);
                $props = $props->getValue($stack[$i]["i"]);
                foreach($props as $pName=>$pValue)
                {
                    for($ptr=&$response,$j=0;$j<count($stack[$i]["r"]);$j++){$ptr = &$ptr[$stack[$i]["r"][$j]];}
                    if(is_object($pValue))
                    {
                        $tmp = $stack[$i]["r"];
                        $tmp[] = $pName;
                        $stack[] = ["r"=>$tmp, "i"=>$pValue];
                    }
                    else $ptr[$pName] = $pValue;
                }
            }
            elseif(is_subclass_of($stack[$i]["i"], Entity\Tabular::class))
            {
                for($ptr=&$response,$j=0;$j<count($stack[$i]["r"]);$j++){$ptr = &$ptr[$stack[$i]["r"][$j]];}
                $element = $stack[$i]["i"]->getContent();
                if(empty($element))
                {
                    $ptr = [];
                }
                else foreach($element as $j => $v)
                {
                    if(is_object($v))
                    {
                        $obj = [];
                        $r = new \ReflectionClass($v);
                        $ps = $r->getProperties();
                        foreach($ps as $p)
                        {
                            $obj[$p->getName()] = $v->{"get".ucfirst($p->getName())}();
                            //\Oculie\Debug::dump([$p->getName(), $obj], FALSE);
                        }
                        $ptr[$j] = $obj;
                        $tmp = $stack[$i]["r"];
                        $tmp[] = $j;
                        $stack[] = ["r"=>$tmp, "i"=>$v];
                    }
                    else $ptr[$j] = $v;
                }
            }
            else
            {
                if(empty($props))
                {
                    for($ptr=&$response,$j=0;$j<count($stack[$i]["r"]);$j++){$ptr = &$ptr[$stack[$i]["r"][$j]];}
                    $ptr = [];
                }
                else foreach($props as $prop)
                {
                    for($ptr=&$response,$j=0;$j<count($stack[$i]["r"]);$j++){$ptr = &$ptr[$stack[$i]["r"][$j]];}
                    $v = $stack[$i]["i"]->{"get".ucfirst($prop->getName())}();
                    if(is_object($v))
                    {
                        $ptr[$prop->getName()] = [];
                        $reflection = new \ReflectionClass(get_class($v));
                        $tmp = $stack[$i]["r"];
                        $tmp[] = $prop->getName();
                        $stack[] = ["r"=>$tmp, "i"=>$v];
                    }
                    else
                    {
                        $ptr[$prop->getName()] = $v;
                    }
                }
            }
        }
        return $response;
    }
}