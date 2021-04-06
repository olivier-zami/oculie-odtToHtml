<?php
namespace Oculie\Core\DataObject\Resource;

class Javascript
{
    /*
     * Behavior
     */

    public function setId($id){$this->id = $id;}
    public function getId(){return $this->id;}

    public function setLocation($location){$this->location = $location;}
    public function getLocation(){return $this->location;}

    public function setContent($content){$this->content = $content;}
    public function getContent(){return $this->content;}

    /*
     * Routines & Procedures
     */

    protected $id       = NULL;
    protected $location = NULL;
    protected $content  = NULL;
}