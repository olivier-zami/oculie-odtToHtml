<?php
namespace Oculie\Core;

class Handler extends \Oculie\Core\Abstraction\Pattern\Handler
{
    /*
     * Behavior
     */

    public function handle($object)
    {
        $this->object = $object;
    }

    public function getObject()
    {
        return $this->object;
    }

    /*
     * Properties & routines
     */

    protected $object;
}