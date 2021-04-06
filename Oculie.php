<?php
class Oculie extends Oculie\Core\Abstraction\Pattern\Extension
{

    /*
     * Interface
     */

    public static function getResourceLocation($filename): string
    {
        $calledClass = get_called_class();
        if(!isset(self::$extension[$calledClass])) self::initExtension($calledClass);
        if(!isset(self::$resourceLocation[$filename])) throw new \Exception("unknown resource \"".$filename."\"");

        $resourceLocation = NULL;
        if(self::$resourceLocation[$filename] == -1)
        {
            $resourceLocation = self::$extension[$calledClass]->getResourcesLocation();
        }
        else $resourceLocation = self::$resourceLocation[$filename];

        return $resourceLocation;
    }

    /*
     *  Routines & Properties
     */

    protected static function initExtension($calledClass)
    {
        self::$extension[$calledClass] = new $calledClass();//TODO: check $class extends \Oculie
        foreach(self::$extension[$calledClass]->getResourcesLocation() as $label => $extensionResourceLocation)
        {
            if(isset(self::$resourceLocation[$label]))
            {
                self::$resourceLocation[$label] = -1;
            }
            else self::$resourceLocation[$label] = $extensionResourceLocation;
        }
    }

    protected static $extension         = [];
    protected static $resourceLocation  = [];

    protected $resourcesLocation = [
        "oculie.js" => __DIR__ . "/resources/js/oculie-v2.js"
    ];



/**********************************************************************************************************************/

    private static $instance    = [];//MEMO: for inquired object
    private static $rscSystem   = NULL;

    /*
     * Behavior
     */

    const CORE_NAMESPACE        = Oculie\Core::class;
    const EXTENSION_NAMESPACE   = Oculie\Extension::class;

    public static function addExtension($extensionName, $classname)
    {
        self::$extension[$extensionName] = new class() {
            public $rootClass;
            public function getClass($classname){return call_user_func_array([$this->rootClass, "getClassOverride"], [$classname]);}
            public function getClasses(){return call_user_func_array([$this->rootClass, "getClassesOverride"], []);}
        };
        self::$extension[$extensionName]->rootClass = $classname;
    }

    public static function getExtension($extensionName)
    {
        return self::$extension[$extensionName];
    }

    public static function getDirectory() //MEMO: protected ?
    {
        if(!isset(self::$rscSystem)) self::$rscSystem = new \Oculie\Core\Register\Resource();
        return self::$rscSystem;
    }

    public static function inquire($subject)
    {
        if(is_object($subject))
            self::inquire_object($subject);
        elseif(is_string($subject) && class_exists($subject))
            self::inquire_class($subject);
    }

    /*
     * Routines & Properties
     */

    protected static function init()
    {
        \Oculie\Debug::log("Loading system core ...");
        include_once(__DIR__ . "/library/Action/showResource.php");
        spl_autoload_register(function($className){
            /*
            \Oculie\Debug::dump([$className], FALSE);
            $rootDir = __DIR__."/../../../var/system";
            $tmp = explode("\\", $className);
            if(isset($tmp[0]) && $tmp[0]=="Oculie" && isset($tmp[1]) && $tmp[1]=="System")
            {
                array_shift($tmp);
                array_shift($tmp);
            }
            $filePath = implode("/", $tmp);
            $srcFile = $rootDir."/".$filePath.".php";
            \Oculie\Debug::dump($srcFile, FALSE);
            if(!file_exists($srcFile))
            {
                if(!is_dir($dirName = dirname($srcFile)))mkdir($dirName, 0777, TRUE);
                if(!file_exists($srcFile))file_put_contents($srcFile, "");
                $className = array_pop($tmp);
                $namespace = "Oculie\\System\\".implode("\\", $tmp);
                $src = "<?php";
                $src .= "\n"."namespace Oculie\\System\\".$namespace.";\n";
                $src .= "\n"."Pattern ".$className;
                $src .= "\n"."{";
                $src .= "\n"."}";
                file_put_contents($srcFile, $src);
                //TODO: verifier fichier et creer exception s

            }
            $database = \Oculie\Core\Register\Model::getDatabase("record");
            $dbName = $database->getConnection()->getDatabase();
            $sm = $database->getConnection()->getSchemaManager();
            */

            /**********************************************************************************************************
            //!verification de l'existence d'une table en bdd
            $classBasename = basename($filePath);
            $recTable = "rec_".lcfirst($classBasename);
            $recTableExist = FALSE;
            $tables = $sm->listTables();
            foreach($tables as $table)
            {
            if($table->getName()==$recTable)
            {
            $recTableExist = TRUE;
            break;
            }
            }

            if(!$recTableExist)
            {
            $schema = new \Doctrine\DBAL\Schema\Schema();
            $tableDef = $schema->createTable($recTable);
            $tableDef->addColumn("id", "integer", ["unsigned"=>TRUE]);
            $tableDef->setPrimaryKey(["id"]);
            $fields = \Oculie\Core\Register\Model::getRepository(lcfirst($classBasename))->getFields();
            foreach($fields as $fName => $fType)
            {
            preg_match("|([a-zA-Z_ ]+)(\(([0-9a-zA-Z_ ]+)\))?|", $fType, $match);

            $type = "undefined";
            $options = [];
            if(strstr($fType, "char"))
            {
            $type = "char";
            }
            elseif(strstr($fType, "date"))
            {
            $type = "date";
            }
            elseif(strstr($fType, "float"))
            {
            $type = "float";
            }
            elseif(strstr($fType, "int"))
            {
            $type = "integer";
            if(isset($match[3]))
            {
            if(strstr($match[3], "MEDIUM"))
            {
            $options["length"] = 32;
            }
            else $options["length"] = $match[3];
            }
            $tableDef->addColumn($fName, $type, $options);
            }
            elseif(strstr($fType, "string"))
            {
            $type = "string";
            if(isset($match[3]))
            {
            if(strstr($match[3], "MEDIUM"))
            {
            $options["length"] = 32;
            }
            else $options["length"] = $match[3];
            }
            $tableDef->addColumn($fName, $type, $options);
            }

            \Oculie\Debug::dump(["col"=>$fName, "type"=>$type, "options"=>$options], FALSE);
            }

            \Oculie\Debug::dump([$fields], FALSE);
            }
            else
            {

            }


            $platform = $database->getConnection()->getDatabasePlatform();
            $test = $schema->toSql($platform);
            \Oculie\Debug::dump([$test]);

            /**********************************************************************************************************/

            /*
            $databases = \Oculie\Core\Register\Model::getDatabase("dbSchema")->getHandler()
                ->select("TABLE_SCHEMA", "TABLE_NAME")
                ->from("tables");
            foreach($databases as $name => $database)
            {
                echo "<br/>".print_r($database, TRUE);
            }
            */

            /*
            echo "<br/>Chargement de \"".$className."\"";
            \Oculie\Debug::dump(["databases"=>$databases]);
            /**********************************************************************************************************/
        });
    }

    protected static function load_extensions()
    {
        \Oculie\Debug::log("Loading system extensions ...");
        $directories = scandir(__DIR__."/..");
        foreach($directories as $directory)
        {
            if(in_array($directory, [".", ".."]))continue;
            if(file_exists($file = __DIR__."/../".$directory."/extension.php"))
            {
                \Oculie\Debug::log("Extension \"".$directory."/extension.php\" found", TRUE);
                include $file;
            }
        }
        \Oculie\Debug::log("Extension Loading Report :");
    }

    protected static function bind($object)
    {
        \Oculie\Debug::dump("Liaison de l'objet ".get_class($object)."");
    }

    protected static function inquire_class($class)
    {
        \Oculie\Debug::dump(["=====> Recherche info sur la classe \"".$class."\""], FALSE);
    }

    protected static function inquire_object($object)
    {
        $hash = spl_object_hash($object);
        $subject = NULL;
        if(isset(self::$instance[$hash]))
        {
            $subject = self::$instance[$hash];
        }
        else
        {
            foreach(self::$extension as $extension)
            {
                $extensionInstance = call_user_func_array([$extension, "getAll"],[]);
                foreach($extensionInstance as $name => $instance)
                {
                    $currentHash = spl_object_hash($instance);
                    if($currentHash != $hash) continue;
                    self::$instance[$currentHash] = call_user_func_array([$extension, "getInformationFrom"], [$instance]);
                    $subject = self::$instance[$currentHash];
                }
                if(isset($subject)) break;
            }
            $test = call_user_func_array([$extension, "getAll"],[]);
            \Oculie\Debug::dump(["post construction" => $test, "hash" => spl_object_hash($test["blogArticle"])], FALSE);
        }
        \Oculie\Debug::dump(["instances ....", self::$instance], FALSE);
        return $subject;
    }
}