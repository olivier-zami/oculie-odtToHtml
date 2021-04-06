<?php
namespace Oculie\Core\DataObject;

class DbConnection extends \Oculie\Core\Abstraction\Model\DbConnection
{
    /*
     * Behavior
     */

    public function setCredentials($credentials)
    {
        $this->credentials = $credentials;
    }

    public function getCredentials()
    {
        return $this->credentials;
    }

    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    /*
     * Properties and routines
     */
}