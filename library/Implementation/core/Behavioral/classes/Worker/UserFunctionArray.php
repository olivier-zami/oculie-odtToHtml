<?php
namespace Oculie\Core\Worker;

class UserFunctionArray extends \Oculie\Core\Worker
{
	public function execute($callable, $parameters)
	{
		return call_user_func_array($callable, $parameters);
	}
}