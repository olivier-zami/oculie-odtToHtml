<?php
namespace Oculie\Core;

abstract class Worker
{
	abstract public function execute($callable, $parameters);
}