<?php
namespace Oculie\Core;

abstract class Builder
	implements \Oculie\Definition\Builder
{
	abstract public static function create();
	abstract public function getInstance();
}