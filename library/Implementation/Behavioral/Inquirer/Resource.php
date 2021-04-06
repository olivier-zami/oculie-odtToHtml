<?php
namespace Oculie\Core\Inquirer;

class Resource
{
    /*
     * Public Static Methods
     */

    public static function inquire($object): Resource
    {
        if(!is_object($object)&&!is_string($object)&&!class_exists($object))throw new \Exception(__METHOD__." take an object or a valid Pattern name as parameter");

        if(is_object($object))
        {
            self::$className = get_class($object);
            self::$object = $object;
        }
        else
        {
            self::$className = $object;
            self::$object = new $object();
        }

        if($object->getLocation())
        {
            self::$pathInfo = pathinfo($object->getLocation());
        }
        else self::$pathInfo = null;

        if(!isset(self::$instance)) self::$instance = new \Oculie\Core\Inquirer\Resource();
        return self::$instance;
    }

    /*
     * Public Instance Methods
     */

    public function getType()
    {
        $extension = isset(self::$pathInfo["extension"]) ? strtolower(self::$pathInfo["extension"]) : "unknown";
        $type = NULL;
        switch($extension)
        {
            case "css":
                $type = \Oculie\Application\Definition\MediaType::CSS;
                break;
            case "html":
                $type = \Oculie\Application\Definition\MediaType::HTML;
                break;
            case "jpeg":
            case "jpg":
                $type = \Oculie\Application\Definition\MediaType::JPG;
                break;
            case "js":
                $type = \Oculie\Application\Definition\MediaType::JAVASCRIPT;
                break;
            case "png":
                $type = \Oculie\Application\Definition\MediaType::PNG;
                break;
            case "svg":
                $type = \Oculie\Application\Definition\MediaType::SVG;
                break;
            default:
                throw new \Exception("in method \"".__METHOD__."\".Inquirer : extension \"".$extension."\" inconnue ...");
                break;
        }
        return $type;
    }

    /*
     * Methods & properties
     */

    protected function __construct(){}

    protected static $instance;
    protected static $className;
    protected static $object;
    protected static $pathInfo;
}