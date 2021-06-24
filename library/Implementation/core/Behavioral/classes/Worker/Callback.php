<?php
namespace Oculie\Core\Worker;

class Callback extends \Oculie\Core\Worker
{
	public function execute($callable, $parameters=[])
	{
		if(!is_object($callable)||(get_class($callable)!=\Oculie\Core\Data\Entity\Callback::class&&(is_subclass_of($callable, \Oculie\Core\Data\Entity\Callback::class))))
		{
			throw new \Exception(__METHOD__ . " : first parameter must be a ".\Oculie\Core\Data\Entity\Callback::class." object");
		}

		$method = $callable->getMethod();
		$parameters = $callable->getParameters();

		$args = [];
		foreach($parameters as $arg)
		{
			if(is_object($arg)&& (get_class($arg) == \Oculie\Core\Data\Entity\Callback::class || is_subclass_of($arg, \Oculie\Core\Data\Entity\Callback::class)))
			{
				$arg = $this->execute($arg);
			}
			elseif(is_object($arg)&&(get_class($arg)==\Closure::class||is_subclass_of($arg, \Closure::class)))
			{
				$arg = \Oculie\Core\Worker\executionMethods::execute($arg);
			}
			$args[] = $arg;
		}

		return \Oculie\Core\Worker\executionMethods::execute($method, $args);
	}
}