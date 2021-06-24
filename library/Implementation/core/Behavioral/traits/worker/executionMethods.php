<?php
namespace Oculie\Core\Worker;

use Oculie\Core\Worker\Closure;
use Oculie\Core\Worker\UserFunctionArray;

trait executionMethods
{
	public function execute()
	{
		$arg = func_get_args();
		$response = NULL;
		if(!isset($arg[0]) || empty($arg[0])) throw new \Exception(__METHOD__ . " must have at least 1 parameter.");

		switch(gettype($arg[0]))
		{
			case "array":
				if(!is_callable($arg[0]))throw new \Exception(__METHOD__ . " arg(1) : ".print_r($arg[0], 1)." is not callable.");
				if(isset($arg[1])&&!is_array($arg[1])) throw new \Exception(__METHOD__ . " : arg(2) should use array to set parameters");
				$callable = $arg[0];
				$parameters = isset($arg[1]) ? $arg[1] : [];
				$worker = new \Oculie\Core\Worker\UserFunctionArray();
				$response = $worker->execute($callable, $parameters);
				break;
			case "object":
				if(get_class($arg[0])==\Closure::class||is_subclass_of($arg[0], \Closure::class))
				{
					if(isset($arg[1])&&!is_array($arg[1])) throw new \Exception(__METHOD__ . " : arg(2) should use array to set parameters");
					$callable = $arg[0];
					$parameters = isset($arg[1]) ? $arg[1] : [];
					$worker = new \Oculie\Core\Worker\Closure();
					$response = $worker->execute($callable, $parameters);
				}
				elseif(get_class($arg[0])==\Oculie\Core\Data\Entity\Callback::class||is_subclass_of($arg[0], \Oculie\Core\Data\Entity\Callback::class))
				{
					$callable = $arg[0];
					$worker = new \Oculie\Core\Worker\Callback();
					$response = $worker->execute($callable);
				}
				elseif(isset($arg[1]) && is_callable($callable = [$arg[0], $arg[1]]))
				{
					if(isset($arg[2])&&!is_array($arg[2])) throw new \Exception(__METHOD__ . " : arg(3) should use array to set parameters");
					$parameters = isset($arg[2]) ? $arg[2] : [];
					$worker = new \Oculie\Core\Worker\UserFunctionArray();
					$response = $worker->execute($callable, $parameters);
				}
				else
				{
					throw new \Exception(__METHOD__ . " argument is not callable.");
				}
				break;
			case "string":
				if(function_exists($arg[0]))
				{
					if(isset($arg[1])&&!is_array($arg[1])) throw new \Exception(__METHOD__ . " : arg(2) should use array to set parameters");
					$callable = $arg[0];
					$parameters = isset($arg[1]) ? $arg[1] : [];
					$worker = new \Oculie\Core\Worker\UserFunctionArray();
					$response = $worker->execute($callable, $parameters);
				}
				elseif(class_exists($arg[0]))
				{
					if(!isset($arg[1]))throw new \Exception(__METHOD__ . " : arg(2) method for class \"".$arg[0]."\"  is not set");
					if(!method_exists($arg[0], $arg[1])) throw new \Exception(__METHOD__ . " : arg(2) class \"".$arg[0]."\" is found but method \"".$arg[1]."\" is not");
					if(isset($arg[2])&&!is_array($arg[2])) throw new \Exception(__METHOD__ . " : arg(3) should use array to set parameters");
					$callable = [$arg[0], $arg[1]];
					$parameters = isset($arg[2]) ? $arg[2] : [];
					$worker = new \Oculie\Core\Worker\UserFunctionArray();
					$response = $worker->execute($callable, $parameters);
				}
				elseif(is_callable($arg[0]))
				{
					if(isset($arg[1])&&!is_array($arg[1])) throw new \Exception(__METHOD__ . " : arg(2) should use array to set parameters");
					$callable = $arg[0];
					$parameters = isset($arg[1]) ? $arg[1] : [];
					$worker = new \Oculie\Core\Worker\UserFunctionArray();
					$response = $worker->execute($callable, $parameters);
				}
				else
				{
					throw new \Exception(__METHOD__ . ": argument \"".$arg[0]."\" is not callable");
				}
				break;
			default:
				\Oculie\Debug::dump(gettype($arg[0]));
				break;
		}
		return $response;
	}
}