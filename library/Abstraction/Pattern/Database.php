<?php
namespace Oculie\Core\Abstraction\Pattern;

abstract class Database extends \Oculie\Core\Definition\Subroutine\Handler
{
    public abstract function createRepository($repository);
    public abstract function getRepositories();
    public abstract function getRepository($name);
    public abstract function setRepository($name, $dataDefinition);
}