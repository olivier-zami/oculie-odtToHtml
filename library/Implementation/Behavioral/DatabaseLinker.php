<?php
namespace Oculie\Core\Linker;

class Database
{
    protected $handler;

    public function getHandler()
    {
        return $this->handler;
    }

    public function setHandler($handler)
    {
        $this->handler = $handler;
        return $this;
    }
}