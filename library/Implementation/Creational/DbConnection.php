<?php
namespace Oculie\Core\Builder;

class DbConnection
{
    /*
     * Behavior
     */

    public function getInstance()
    {
        $dbConn = new \Oculie\Core\DataObject\DbConnection();
        $dbConn->setUrl($this->url);
        $dbConn->setCredentials($this->credentials);
        $dbConn->setDriver($this->driver);
        return $dbConn;
    }

    public function setCredentials($credentials)
    {
        $this->credentials = $credentials;
        return $this;
    }

    public function setDriver($driver)
    {
        $this->driver = $driver;
        return $this;
    }

    public function setUrl($url)//TODO: ajouter dbname a l'url
    {
        $this->url = $url;
        return $this;
    }

    /*
     * Properties and routines
     */

    private $url;
    private $credentials;
    private $driver;
}