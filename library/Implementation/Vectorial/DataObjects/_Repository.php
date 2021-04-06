<?php
namespace Oculie\Core\DataObject;

class Repository extends \Oculie\Core\Abstraction\Model\Repository
{
    /*
     * Behavior
     */

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setRecordClass($recordClass)
    {
        $this->recordClass = $recordClass;
    }

    public function getRecordClass()
    {
        return $this->recordClass;
    }

    public function setRecordStorage($storageResource=NULL)
    {
        $this->record = $storageResource;
    }

    public function getRecordStorage()
    {
        return $this->record;
    }


    //TODO: corriger avant de virer __call Uncaught Error: Call to undefined method Oculie\Core\Abstraction\Model\Repository::setTitle() in /home/olivier/Projets/rhodophytes.local/src/oculie/core/Action/objects/Builder/Repository1.php:14
    public function __call($func, $p)
    {
        \Oculie\Debug::dump(["repo function" => $func, debug_backtrace()], FALSE);
        $prefix = substr($func, 0, 3);
        if($prefix=="set") $this->field[lcfirst(substr($func, 3))] = isset($p[0])?$p[0]:NULL;
        elseif($prefix=="get")
        {
            return $this->field[lcfirst(substr($func, 3))];
        }
    }


    /*
     * Behavior
     */
    public function getFields()//TODO: a acceder via inquire
    {
        return $this->field;
    }

    public function getNewRecord(){}
    public function getRecordById(){}
    public function getRecordCollection(){}

    public function setRecordType($recordClass)
    {
        if(is_array($recordClass))throw new \Exception("La gestion des records anonymes n'est pas implementé...");//TODO: implementer les records anonymes
        /*
         * TODO: les records anonymes seront gérées par la classe ..\Cire\Model\Record via des methode margique
         * attention! les autres classes records ne devront pas hériter des la classe ..Core\Model\Record
         */
        $this->recordClass = $recordClass;
    }

    /*
     * Variables & Procedures
     */

    protected $name;
    protected $recordClass;
    protected $record       = [];

}