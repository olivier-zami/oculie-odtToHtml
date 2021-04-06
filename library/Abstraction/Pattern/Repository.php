<?php
namespace Oculie\Core\Abstraction\Pattern;

abstract class Repository extends \Oculie\Core\Definition\Subroutine\Handler
{
    public abstract function createRecord();
    public abstract function deleteRecord($record/* or recordCollection*/);
    public abstract function deleteRecordById($id);
    public abstract function getRecord();//$record->asArray(); $record->asEntity(); $record->asDataObject();
    public abstract function getRecordById($id);//return Pattern extends Record {use SqlQueryMethods; }
    public abstract function getRecordCollection();//return Pattern extends RecordCollection implements Iterator {use SqlQueryMethods; }
    public abstract function saveRecord($record/* or recordCollection*/);
}