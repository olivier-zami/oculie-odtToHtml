<?php
namespace Oculie\Core\DataObject;

class Resource
{
    /*
     * Behavior
     */

    public function setId($id){$this->id = $id;}
    public function getId(){return $this->id;}

    public function setType($type){$this->type = $type;}
    public function getType(){return $this->type;}

    public function setLocation($location){$this->location = $location;}
    public function getLocation(){return $this->location;}

    public function setContent($content){$this->content = $content;}
    public function getContent(){return $this->content;}

    /*
     * Routines & Procedures
     */

    protected $id       = NULL;
    protected $type     = NULL;
    protected $location = NULL;
    protected $content  = NULL;
}