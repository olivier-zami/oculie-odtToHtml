<?php
namespace Oculie\Definition;

interface Builder
{
	public static function create();
	public function getInstance();
}