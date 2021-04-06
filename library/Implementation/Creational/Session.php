<?php
namespace Oculie\Core\Builder;

class Session extends \Oculie\Core\Builder
{
    protected $data;
    protected $sessionType;

    public function getInstance()
    {
        $instance = new \Oculie\Core\DataObject\Session();
        return $instance;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function setType($sessionType)
    {
        $this->sessionType = $sessionType;
        return $this;
    }
}