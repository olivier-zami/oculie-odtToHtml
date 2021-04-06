<?php
namespace Oculie\Core\Builder;

class Database extends \Oculie\Core\Builder
{
    protected $credentials;
    protected $driver;
    protected $instance;
    protected $url;

    public function getInstance()
    {
        $this->instance = new \Oculie\Core\DataObject\Database();
        $tmp = explode("::", $this->driver);
        $extNamespace = $tmp[0];

        $dbConnectionBuilder = \Oculie\Core\Register\Extension::get($extNamespace)->getClassOverride(\Oculie\Core\Builder\DbConnection::class);//TODO
        \Oculie\Debug::dump(["Modifier convert Pattern to DataConn", $dbConnectionBuilder], FALSE);
        $this->instance->setConnection((new $dbConnectionBuilder)
            ->setDriver($this->driver)
            ->setUrl($this->url)
            ->setCredentials($this->credentials)
            ->getInstance());

        //$databaseHandler = \Oculie\Core\Register\Extension::get($extNamespace)->getClassOverride(\Oculie\Core\Handler\Repository::Pattern);
        //$this->instance->setHandler($databaseHandler);

        return $this->instance;
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

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
}