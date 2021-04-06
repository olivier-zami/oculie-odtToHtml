<?php
namespace Oculie\Core;

/*
require_once(__DIR__ . "/library/entities/Action.php");
require_once(__DIR__ . "/library/entities/Order.php");
require_once(__DIR__ . "/library/entities/Request.php");
require_once(__DIR__ . "/library/entities/UniformResourceIdentifier.php");

require_once(__DIR__ . "/library/Pattern/JsonHandler.php");
require_once(__DIR__ . "/library/Pattern/DeclarationObject.php");

require_once(__DIR__ . "/library/mandates/ObjectAccessorDescriptor.php");

require_once(__DIR__ . "/library/traits/ChainedSetterTrait.php");
*/


use Oculie\Entity\Callback;
use Oculie\JsonHandler;

class Processor
{
    const CORE_DIR = __DIR__;
    const AUTOREFENCE_OBJECT = "self::";
    const MAIN_CONTROLLER_OBJECT = "system::";

    const PROCESS_TYPE_DIRECT_RESOURCE_ACCESS = 1;

    protected static $instanceName              = NULL;

    private static $builderProcedure                = array();
    private static $builderProcedureInterface       = NULL;
    private static $builderProcedureInterfaceName   = NULL;
    private static $currentProcessObject        = NULL;
    private static $idxInstruction              = NULL;
    private static $nextLabel                   = NULL;
	private static $class                       = array();
    private static $config                      = array();
    private static $controller                  = NULL;
    private static $environment                 = array();
    private static $exceptionHandler            = NULL;
    private static $instruction                 = array();
    private static $interface                   = array();
    private static $object                      = array();
    private static $path                        = array();
    private static $register                    = array();
    private static $stack                       = array();
    private static $stopExecution               = FALSE;
    private static $task                        = array();


    protected static function addInstruction($callback, $parameters=array())
    {
        self::$instruction[] = array($callback, $parameters);
    }

    protected static function declare($instanceName, $className, $parameters=array())
    {
        self::$object[$instanceName] = new Oculie\DeclarationObject($className, $parameters);
        return self::$object[$instanceName];
    }

    protected static function exec($process)
    {
        $noExecutionFlag = FALSE;
        if(!is_object($process[0][0]))switch($process[0][0])
        {
            case self::MAIN_CONTROLLER_OBJECT:
                if(!is_object(self::$controller) && $process[0][1]=="__construct")
                {
                    $class = new ReflectionClass(self::$controller[0]);
                    self::$controller = $class->newInstanceArgs($process[1]);
                    $noExecutionFlag = TRUE;
                }
                $process[0][0] = self::$controller;
                break;
            case self::AUTOREFENCE_OBJECT:
                $process[0][0] = self::$currentProcessObject;
                break;
        }
        if(!$noExecutionFlag)
        {
            self::$currentProcessObject = $process[0][0];
            $method = $process[0];
            $parameters = $process[1];

            if(isset($parameters[0]) && is_object($parameters[0]) && isset($parameters[0]->instruction))
            {
                switch($parameters[0]->instruction)
                {
                    case "pop":
                        $parameters[0] = self::pop();
                        break;
                }
            }

            try
            {
                call_user_func_array($method, $parameters);
            }
            catch(\Exception $e)
            {
                if(!self::handleException($e))
                {
                    //TODO: gerer la gestion d'exception par defaut
                    self::stopExecution(TRUE);
                }
                /*
                self::$nextLabel = self::$idxInstruction;
                self::interrupt($e);
                //TODO: gerer les boucle sans fin (ex config core max chained exception allowed = 5)
                //die("@file ".__FILE__." line".__LINE__." : no exception handler for now -< endless loop to handle");
                */
            }
        }
    }

    protected static function getMockInterface($instanceName)
    {
        self::$instanceName = $instanceName;
        if(!isset(self::$interface["builder"]))
        {
            self::$interface["builder"] = new class() extends Oculie
            {
                public function __call($name, $parameters)
                {
                    return new Oculie\ObjectAccessorDescriptor(self::$instanceName, Oculie\ObjectAccessorDescriptor::CALL, $name, $parameters);
                }

                public function __get($name)
                {
                    return new Oculie\ObjectAccessorDescriptor(self::$instanceName, Oculie\ObjectAccessorDescriptor::GET, $name);
                }
            };
        }
        return self::$interface["builder"];
    }

    protected static function getObjectValue($ObjectAccessorDescriptor)
    {
        if(get_class(self::$object[$ObjectAccessorDescriptor->instance]) == Oculie\DeclarationObject::class)
        {
            self::$object[$ObjectAccessorDescriptor->instance] = self::$object[$ObjectAccessorDescriptor->instance]->__destruct()->getData();
        }

        $objectValue = NULL;
        if($ObjectAccessorDescriptor->methodType == Oculie\ObjectAccessorDescriptor::CALL)
        {
            $function = $ObjectAccessorDescriptor->methodName;
            $objectValue = call_user_func_array(array(self::$object[$ObjectAccessorDescriptor->instance], $function), $parameters);//TODO: gestion des parametres
        }
        elseif($ObjectAccessorDescriptor->methodType == Oculie\ObjectAccessorDescriptor::GET)
        {
            $property = $ObjectAccessorDescriptor->methodName;
            $objectValue = self::$object[$ObjectAccessorDescriptor->instance]->{$property};
        }
        return $objectValue;
    }

    protected static function handleException($e)
    {
        if(isset(self::$exceptionHandler)) call_user_func_array(self::$exceptionHandler, array($e));
        return isset(self::$exceptionHandler) ? TRUE : FALSE;
    }

    /*
    protected static function hookInstruction($old, $new)
    {
        self::$instruction[$old] = $new;
    }

    protected static function interrupt(\Exception $e)
    {
        if(isset(self::$instruction[strtolower(__FUNCTION__)])) call_user_func_array(self::$instruction[strtolower(__FUNCTION__)], array($e));
        else
        {
            echo "<fieldset><legend>Default ExceptionHandler</legend><pre>".print_r($e, TRUE)."</pre></fieldset>";
            exit();
        }
    }
    */

    protected static function load()
    {
        $args = func_get_args();
        $mainIndexName = get_called_class();
        $ptr = &self::$register[$mainIndexName];
        while(!empty($args))
        {
            $tmp = array_shift($args);
            $ptr=&$ptr[$tmp];
        }
        $outputValue = $ptr;
        unset($ptr);
        return $outputValue;
    }

    protected static function nextInstruction($label=NULL)
    {
        if(!isset($label))
        {
            self::$idxInstruction++;
        }
        /*
        if(isset($label))                                   {self::$idxInstruction = $label; self::$nextLabel = NULL;}
        elseif(!isset($label)&&isset(self::$nextLabel))     {self::$idxInstruction=self::$nextLabel;self::$nextLabel=NULL;}
        else                                                {self::$idxInstruction++ ; self::$nextLabel=NULL;}

        return isset(self::$task[self::$idxInstruction]) ? self::$task[self::$idxInstruction] : NULL;
        */
        return isset(self::$instruction[self::$idxInstruction]);
    }

    protected static function newBuilderProcedure($className)
    {
        self::$builderProcedureInterfaceName = $className;
        if(!isset(self::$builderProcedureInterface))
        {
            self::$builderProcedureInterface = new class() extends Oculie
            {
                public $procedure = array();

                public function __call($methodName, $parameters)
            	{
            		$this->procedure[] = array($methodName, $parameters);
            		return $this;
            	}
            };
        }
        self::$builderProcedureInterface->procedure = array();
        if(!isset(self::$builderProcedure[self::$builderProcedureInterfaceName]))
        {
            self::$builderProcedure[self::$builderProcedureInterfaceName] = array();
        }
        self::$builderProcedure[self::$builderProcedureInterfaceName]["call"] = &self::$builderProcedureInterface->procedure;
        return self::$builderProcedureInterface;
    }

    protected static function newOrder()
    {
        return new class() extends Oculie\Entity\Order
        {
            use Oculie\ChainedSetterTrait;
        };
    }

    protected static function newTrigger()
    {
        return new class()
		{
            use Oculie\ChainedSetterTrait;

			protected $event;
			protected $send;
			protected $to;

			public function onEvent($event)
			{
				$this->event = $event;
				return $this;
			}

			public function send($message)
			{
				$this->send = $message;
				return $this;
			}

			public function to($recipient)
			{
				$this->to = $recipient;
				return $this;
			}
		};
    }

    protected static function prepare($className)
    {
        $instance = new $className();
        foreach(self::$builderProcedure[$className]["call"] as $callback)
        {
            call_user_func_array(array($instance, $callback[0]), $callback[1]);
        }
        return $instance;
    }

    protected static function prepareInstance($className, $parameters=array())
    {
        return new Oculie\DeclarationObject($className, $parameters);
    }

    protected static function push($data)
    {
        array_push(self::$stack, $data);
    }

    protected static function pop()
    {
        return array_pop(self::$stack);
    }

    protected static function register($name)
    {
        $args = func_get_args();
        $extensionName = get_called_class();
        if(!isset(self::$register[$extensionName])) self::$register[$extensionName] = array();
        $ptr = &self::$register[$extensionName];
        while(!empty($args))
        {
            $tmp = array_shift($args);
            if(is_array($ptr)&&is_string($tmp)&&!empty($args))
            {
                if(!isset($ptr[$tmp]))$ptr[$tmp]=array();
                $ptr=&$ptr[$tmp];
            }
            else
            {
                $ptr = $tmp; break;
            }
        }
        unset($ptr);
    }

    protected static function setBuilderProcedureFinal($className, $methodName)
    {
        if(!isset(self::$builderProcedure[$className]))
        {
            self::$builderProcedure[$className] = array();
        }
        self::$builderProcedure[$className]["final"] = $methodName;
    }

    protected static function show($viewer)
    {
        $output = call_user_func_array(array($viewer, self::$builderProcedure[get_class($viewer)]["final"]), array());
        if(is_string($output)) echo $output;
        return $output;
    }

    protected static function stopExecution($cond=FALSE)
    {
        if($cond) self::$stopExecution = TRUE;
        return self::$stopExecution;
    }

    protected static function systemInit($parameters=array())
    {
        if(!is_array(self::$controller)) throw new Exception("Echec dans l'instanciation du controlleur : controlleur déjà instancié.");
        $class = new ReflectionClass(self::$controller[0]);
        self::$controller = $class->newInstanceArgs($parameters);
    }
}
?>
