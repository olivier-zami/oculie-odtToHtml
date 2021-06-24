<?php
namespace Oculie\Core\Data\Entity;

class Callback extends \Oculie\Core\Data\Callback
{
    /*
     * Interface
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setParameters($mixed=NULL)
    {
        $this->parameters = func_get_args();
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    /*
     * Routines & Properties
     */

    protected $method       = NULL;
    protected $parameters   = [];
}