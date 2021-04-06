<?php
namespace Oculie\Core\Abstraction\Pattern;

abstract class Extension
{
    /*
     * Interface
     */

    public function getResourcesLocation()
    {
        return $this->resourcesLocation;
    }

    /*
     * Routines & Properties
     */
    protected $resourcesLocation;
}