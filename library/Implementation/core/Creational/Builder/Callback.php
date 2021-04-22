<?php
namespace Oculie\Core\Builder;

class Callback /*extends \Oculie\Core\Abstraction\Pattern\Builder*/
{
    /*
     * Interface
     */

    public static function create()
    {
        if(!isset(self::$builder))self::$builder = new \Oculie\Core\Builder\Callback();
        $arg = func_get_args();
        $response = NULL;
        switch(count($arg))
		{
			case 0:
				$response = self::$builder;
				break;
			case 1:
				self::$builder->setMethod($arg[0]);
				$response = self::$builder->getInstance();
				break;
			case 2:
				self::$builder->setMethod($arg[0]);
				call_user_func_array(self::$builder, $arg[1]);
				$response = self::$builder->getInstance();
				break;
			default:
				throw new \Exception(__METHODS__." take 0, 1 or 2 parameter(s).");
				break;
		}
        return $response;
    }

    public function __construct()
    {
    	$this->type			= NULL;
        $this->method 		= NULL;
        $this->parameters 	= [];
    }

    public function setMethod($method): Callback
    {
    	if(!is_callable($method))throw new \Exception("In \"".__METHOD__."\" : first parameter must be callable.");

    	if(is_string($method))
		{
			$this->type = \Oculie\Core\Definition\Callback::STRING;
		}
    	else if(is_array($method))
		{
			$this->type = \Oculie\Core\Definition\Callback::ARRAY;
		}
    	else if(is_object($method))
		{
			$this->type = \Oculie\Core\Definition\Callback::OBJECT;
		}
    	else $this->type = \Oculie\Core\Definition\Callback::UNDEFINED;

        $this->method = $method;
        return $this;
    }

    public function setParameters(): Callback
    {
        $this->parameters = func_get_args();
        return $this;
    }

    public function getInstance(): \Oculie\Core\Callback
    {
        $instance = new \Oculie\Core\Callback();
        $instance->setType($this->type);
        $instance->setMethod($this->method);
        call_user_func_array([$instance, "setParameters"], $this->parameters);
        $this->type			= NULL;
        $this->method		= NULL;
        $this->parameters 	= [];
        return $instance;
    }

    /*
     * Routines & Properties
     */

    protected static $builder;

    private $type;
    private $method;
    private $parameters;
}