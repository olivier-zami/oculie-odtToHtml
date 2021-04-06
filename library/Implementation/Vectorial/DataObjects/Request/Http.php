<?php
namespace Oculie\Core\DataObject\Request;

class Http extends \Oculie\Core\DataObject\Request
{
    /*
     * Behavior
     */

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setQueryData($queryData)
    {
        $this->queryData = $queryData;
    }

    public function getQueryData()
    {
        return $this->queryData;
    }

    public function setFormData($formData)
    {
        $this->formData = $formData;
    }

    public function getBodyData()
    {
        return $this->formData;
    }

    public function setRawData($rawBodyData)
    {
        $this->rawData = $rawBodyData;
    }

    public function getRawData()
    {
        return $this->rawData;
    }

    /*
     * Routines & Properties
     */

    protected $method;
	protected $uri;
	protected $queryData;
	protected $formData;
	protected $rawData;
}
?>