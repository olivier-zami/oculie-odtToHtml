<?php
namespace Oculie\Core;

class Callback extends \Oculie\Core\Definition\Model\Callback
{
    /*
     * Interface
     */

	public function setType($type)
	{
		$this->type = $type;
	}

	public function getType()
	{
		return $this->type;
	}

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

	protected $type			= NULL;
    protected $method       = NULL;
    protected $parameters   = [];
}
?>