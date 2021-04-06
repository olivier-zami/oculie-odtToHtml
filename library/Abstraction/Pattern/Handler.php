<?php
namespace Oculie\Core\Abstraction\Pattern;

abstract class Handler
{
    abstract public function handle($object);
    abstract public function getObject();
}