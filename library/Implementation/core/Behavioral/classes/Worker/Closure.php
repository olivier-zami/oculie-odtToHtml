<?php
namespace Oculie\Core\Worker;

class Closure extends \Oculie\Core\Worker
{
	public function execute($callable, $parameters=[])
	{
		if(!is_object($callable)||(get_class($callable)!=\Closure::class&&!is_subclass_of($callable, \Closure::class)))
		{
			throw new \Exception(__METHOD__ . " : use closure object as parameter");
		}

		if(!is_array($parameters))
		{
			throw new \Exception(__METHOD__ . " : second parameters should be an array");
		}

		$args = [];
		foreach($parameters as $arg)
		{
			if(is_object($arg)&&(get_class($arg)==\Closure::class||is_subclass_of($arg, \Closure::class)))
			{
				$arg = $this->execute($arg);
			}
			elseif(is_object($arg)&& (get_class($arg) == \Oculie\Core\Data\Entity\Callback::class || is_subclass_of($arg, \Oculie\Core\Data\Entity\Callback::class)))
			{
				$arg = \Oculie\Core\Worker\executionMethods::execute($arg);
			}
			$args[] = var_export($arg, TRUE);
		}

		$response = NULL;
		$call = "\$response = \$callable(".implode(",", $args).");";
		eval($call);

		return isset($response) ? $response : NULL;

	}
}