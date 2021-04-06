<?php
namespace Oculie\Core\DataObject;

class Database
{
    /*
     * Behavior
     */

    public function setConnectionObject($dataConn)
    {
        $this->dataConn = $dataConn;
    }

    public function getConnectionObject()
    {
        return $this->dataConn;
    }

    /*
    public function getHandler()
    {
        return $this->handler;
    }

    public function setHandler($handler)
    {
        $this->handler = $handler;
    }
    */

    /*
     * Variables & Routines internes
     */

    protected $dataConn;
    //protected $handler;
    protected $repository       = [];
}