<?php
namespace Oculie\Core\Builder;

class Repository extends \Oculie\Core\Abstraction\Pattern\Builder
{
    public function getInstance()
    {
        $instance = new \Oculie\Core\DataObject\Repository();
        if(isset($this->recordClass))$instance->setRecordClass($this->recordClass);
        return $instance;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setRecordClass($recordClass)
    {
        $this->recordClass = $recordClass;
        return $this;
    }

    /*
     * Properties & routines
     */

    protected $name;
    protected $instance;
    protected $recordClass;
}