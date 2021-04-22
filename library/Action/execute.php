<?php
namespace Oculie;

function execute($action)
{
	$response = NULL;
	switch($action->getType())
	{
		case \Oculie\Core\Definition\Callback::STRING:
		case \Oculie\Core\Definition\Callback::ARRAY:
			$response = call_user_func_array($action->getMethod(), $action->getParameters());
			break;
		case \Oculie\Core\Definition\Callback::OBJECT:
			switch(count($arg = $action->getParameters()))
			{
				case 0:
					$response = $action->getMethod()();
					break;
				case 1:
					$response = $action->getMethod()($arg[0]);
					break;
				case 2:
					$response = $action->getMethod()($arg[0], $arg[1]);
					break;
				case 3:
					$response = $action->getMethod()($arg[0], $arg[1], $arg[2]);
					break;
			}
			break;
		default:
			throw new \Exception("Unknown Callback type ...");
			break;

	}
	return $response;
}