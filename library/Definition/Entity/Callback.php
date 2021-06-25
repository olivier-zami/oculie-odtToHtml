<?php
namespace Oculie\Definition\Entity;

abstract class Callback
{
	/*
	 * Interface
	 */
	abstract function setMethod($method);
	abstract function setParameters();

	/*
	 * Routines & Properties
	 */
    protected $method;
    protected $parameters;
}