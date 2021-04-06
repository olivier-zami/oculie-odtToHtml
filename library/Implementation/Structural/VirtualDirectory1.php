<?php
namespace Oculie\Core\Structural\Iterator;

class VirtualDirectory extends \Oculie\Core\Structural\Directory
{
    private $idx            = 0;
    private $cpt1           = 0;
    private $cpt2           = 0;
    private $urlLocation    = [];
    private $urlLocator     = [];

    public function __construct($param = [])
    {
        $this->cpt1 = 0;
        $this->idx = $this->cpt1;
        if(isset($param["location"])) $this->urlLocation = $param["location"];
        if(isset($param["locator"])) $this->urlLocator = $param["locator"];//MEMO: contient des methodes qui listent des repertoires
    }

    public function current()
    {
        return $this->urlLocation[$this->idx];
    }

    public function key ()
    {
        return $this->idx;
    }

    public function next()
    {
        if($this->cpt1 < count($this->urlLocation))
        {
            $this->cpt1++;
        }
        $this->idx = $this->cpt1;
    }

    public function rewind()
    {
        $this->idx = 0;
    }

    public function valid()
    {
        $validity = FALSE;
        if($this->idx<count($this->urlLocation) && isset($this->urlLocation[$this->idx]))
        {
            $validity = TRUE;
        }
        return $validity;

    }
}