<?php
namespace Oculie;

class VirtualFileSystem //extends or implement \Oculie\Core\Composite
{
    /*
     * Behavior
     */

    /*
    CompositeInterface :
    do()
    add()
    remove()
    getChild()
     */

    public function __construct($option=NULL)
    {
        if(isset($option))
        {
            $this->rootLocation = "";
        }
        else $this->rootLocation = $_SERVER["DOCUMENT_ROOT"];
    }

    public function setRootLocation($rootLocation)
    {
        \Oculie\Debug::log("setting root directory : value = \"".$rootLocation."\"");
        $this->rootLocation = $rootLocation;
    }

    public function addResource($label, $resource)
    {
        $this->virtualLocation[$label] = $resource;
    }

    public function getResource($location): ?Core\DataObject\Resource
    {
        $resource = NULL;

        if(file_exists($this->rootLocation.$location))
        {
            $resource = new \Oculie\Core\DataObject\Resource();
            $resource->setType("");//TODO: definir le type de resource
            $resource->setLocation($this->rootLocation.$location);
        }
        else
        {
            $resource = NULL;
            if(isset($this->virtualLocation[$location]))
            {
                $resource = $this->virtualLocation[$location];
            }
            else
            {
                $baseLocationAlias = "";
                foreach($this->virtualLocation as $alias => $resource)
                {
                    if(strlen($alias)>strlen($location))continue;
                    if(!strncmp($alias, $location, strlen($alias)) && (strlen($alias)>strlen($baseLocationAlias)))
                    {
                        $baseLocationAlias = $alias;
                        continue;
                    }
                }
            }
        }

        return $resource;
    }

    public function delete($object)
    {

    }

    public function save($label, $object=NULL)
    {
        /*TODO: a utiliser dans un test pour l'organisation de la librarie
        $dump = is_subclass_of($object, \Oculie\Core\Structural\Iterator\VirtualDirectory1::Pattern);
        \Oculie\Debug::dump([
            "Pattern"             => get_class($object),
            "cond:subclassOf"   => \Oculie\Core\Structural\Directory::Pattern,
            "result"            => is_subclass_of($object, \Oculie\Core\Structural\Directory::Pattern)
        ], FALSE);
        */
        if(!is_object($object)
            || !(is_subclass_of($object, \Oculie\Core\Abstraction\Vectorial\File::class) || is_subclass_of($object, \Oculie\Core\Structural\Directory::class))
        )
        {
            throw new \Exception("Method \"".__METHOD__."\" must have a ".\Oculie\Core\DataObject\VirtualFile::class." object or a ".\Oculie\Core\Structural\Iterator\VirtualDirectory1::class." object as 2nd parameters : ".get_class($object)." given");
        }
        $this->file[$label] = $object;
    }

    public function select($label)
    {
        if(!isset($this->selectInterface))
        {
            //TODO: utiliser inquierer ?
            $this->selectInterface = new \Oculie\Core\DataObject\ResourceLocation();
        }

        if(isset($this->file[$label]))
        {
            \Oculie\Debug::log("Chargement du fichier \"".$label."\" en tant qu'objet ".get_class($this->file[$label]));

            /*
            \Oculie\Debug::dump([
                "Pattern"             => get_class($this->file[$label]),
                "cond:subclassOf"   => \Oculie\Core\Abstraction\Model\File1::Pattern,
                "result"            => is_subclass_of($this->file[$label], \Oculie\Core\Abstraction\Model\File1::Pattern)
            ], FALSE);
            */

            if(is_subclass_of($this->file[$label], \Oculie\Core\Abstraction\Vectorial\File::class))
            {
                $this->selectInterface->setFile($this->file[$label]);
            }
        }
        else
        {
            \Oculie\Debug::log("Recherche du fichier \"" . $label . "\" avant chargement");
            foreach ($this->file as $name => $object)
            {
                if(strlen($label)<strlen($name) || strncmp($label, $name, strlen($name))) continue;
                if(is_subclass_of($object, \Oculie\Core\Structural\Directory::class))
                {
                    \Oculie\Debug::log("Recherche de \"".$label."\" dans le repertoire virtuel \"".$name."\" => ...");
                    \Oculie\Debug::log();
                    //\Oculie\Debug::dump($object, FALSE);
                    foreach($object as $key => $subObject)
                    {
                        //echo "<br/>in file \"".__FILE__."\"label:".$label." [".$key."] => ".print_r($subObject, TRUE);
                    }
                }
            }
        }

        return $this->selectInterface;
    }

    public function update($object)
    {

    }

    /*
     * Routines & Procedures
     */

    private $rootLocation;
    private $virtualLocation    = [];
    private $file = [];
    private $selectInterface;
    private $updateInterface;
}