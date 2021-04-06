<?php
class BreadthFirstSearch implements Iterator
{
    private $idx;
    private $childIndexName;
    private $iteration = [];
    private $path = [];

    public function __construct($reference, $childIndex)
    {
        $this->idx = 0;
        $this->childIndexName = $childIndex;
        if(is_array($reference))//TODO si tableau parcours tableau, si objet $this->iteration[] = $reference et parcours enfant
        {
            $this->iteration = $reference;
            for($i=0;$i<count($this->iteration);$i++)
            {
                $path = [];
                $path[] = $i;
                $this->path[] = $path;
            }
        }
    }

    public function current()
    {
        //\Oculie\Debug::dump($this->iteration, FALSE);
        if(is_object($this->iteration[$this->idx]))
        {
            if(isset($this->iteration[$this->idx]->{$this->childIndexName}))
            {
                $child = $this->iteration[$this->idx]->{$this->childIndexName};
            }
            else $child = [];
        }
        elseif(is_array($this->iteration[$this->idx]))
        {
            if(isset($this->iteration[$this->idx][$this->childIndexName]))
            {
                $child = $this->iteration[$this->idx][$this->childIndexName];
            }
            else $child = [];
        }
        else throw new \Exception("Tentative de parser un objet de type \"".gettype($this->iteration[$this->idx])."\"");

        for($i=0; $i<count($child); $i++)
        {
            $path = $this->path[$this->idx];
            $path[] = $i;
            $this->path[] = $path;
            $this->iteration[] = $child[$i];
        }

        return ["node"=>$this->iteration[$this->idx], "path"=>$this->path[$this->idx]];
    }

    public function key()
    {
        return $this->idx;
    }

    public function next()
    {
        return ++$this->idx;
    }

    public function rewind()
    {
        $this->idx = 0;
    }
    public function valid()
    {
        return isset($this->iteration[$this->idx]);
    }
}