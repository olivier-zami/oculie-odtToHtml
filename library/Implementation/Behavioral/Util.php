<?php
namespace Oculie\Core;

class Util
{
    public static function newChainedMutatorClass($entityName)
    {
        $wraper = NULL;//TODO: changer le trait qui ne correspond pas (copier-coller)
        eval("\$wraper = new Pattern() extends ".$entityName."
        {
            use Oculie\\ChainedSetterTrait;
        };");
        return $wraper;
    }

    protected static function normalizePath($path)
    {
        if(!isset(self::$environment["DIRECTORY_SEPARATOR"])) self::$environment["DIRECTORY_SEPARATOR"] = DIRECTORY_SEPARATOR;
        $path = str_replace(array('/', '\\'), self::$environment["DIRECTORY_SEPARATOR"], $path);
        $parts = array_filter(explode(self::$environment["DIRECTORY_SEPARATOR"], $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(self::$environment["DIRECTORY_SEPARATOR"], $absolutes);
    }


    /******************************************************************************************************************/

    const APP_AUTOLOAD				= "APP_AUTOLOAD";
    const DEFAULT_ACTION_PREFIX		= "ACTION_";
    const LOAD_RECURSIVE 			= "OCULIE.LOAD_RECURSIVE";

    const DEFAULT 					= [
        "API"							=> [],
        "COMMAND"						=> [],
        "WEB"							=> [
            "DATA_TEMPLATE_DIRECTORY"		=> "web",
            "DATA_PROCESSOR_DIRECTORY"	=> "conf/web"
        ],
    ];

    const SRC_DIR = "SRC_DIR";

    protected static $subject;

    private static $action			= [];
    private static $core;
    private static $configuration 	= [];
    private static $register		= [];
    private static $appConfig = [];
    private static $DIRECTORY = [
        self::SRC_DIR => __DIR__
    ];
    private static $sysConfig = [];
    private static $autoload_register = [
        "CLASS"     => [],
        "FUNCTION"  => []
    ];
    private static $builder = [];
    private static $setter = [];
    protected static $registered_class = [];
    private static $service_interface = [];

    public static function autoload($arrayConfigElement)
    {
        if(!is_string($arrayConfigElement) && !is_array($arrayConfigElement)) throw new \Exception("Autoload prend un entier ou un tableau en parametre.");
        if(is_string($arrayConfigElement))
        {
            $arrayConfigElement = [];
        }

        foreach($arrayConfigElement as $element)
        {
            require_once($element);
        }
    }

    public static function builder($classObject)
    {
        //TODO: faire une methode pour verifier le format de la classe
        if(!class_exists($className = "Oculie\\Core\\Builder\\".$classObject)) throw new Exception("la classe ".$className." n'a pas de builder, pas de DTO, pas de fichier ... etc");
        if(!isset(self::$builder[$classObject])) self::$builder[$classObject] = new $className();
        return self::$builder[$classObject];
    }

    public static function createAction($name, $callable)
    {
        self::$action["name"] = $callable;
    }

    public static function data($instance)
    {
        if(!isset(self::$setter[$classObject = basename(get_class($instance))]))//basename(get_class(...)) may be bad by ima lazy dude
        {
            if(!class_exists($className = "Oculie\\Core\\Accessor\\".$classObject));//TODO: creation les classes a chaud si elle n'existent pas si la configuration le permet (dans les var/* ?)
            self::$setter[$classObject] = new $className();
        }
        return self::$setter[$classObject]->get($instance);
    }

    public static function dataDefinition($data)
    {
        return new class()
        {
            public function getDataId(){}//(id)
            public function getDataProcess()//(Process/Model)TODO: recuperer les données via une classe Oculie\Config
            {
                return "Dashboard\Controller\Command\ClearCache";
            }
            public function getModel(){}//(Model)
        };
    }

    public static function get_autoload_register($type)
    {
        //TODO: le type doit etre CLASS ou FUNCTION
        return self::$autoload_register[$type];
    }

    /******************************************************************************************************************/
    //TODO: transformer getAsArrayConfigurationFile en readConfigFile()
    /*
    readConfigFile()->getContentAsArray();
     */

    public static function getAsArrayConfigurationFile($cfg_array_file)
    {
        include_once("handler/PhpData.php");
        $cfg_array = [];
        if(file_exists($cfg_array_file))
        {
            $phpDataHandler = new \Oculie\Handler\PhpData();
            $phpDataHandler->setResource($cfg_array_file);//must me a Resource Pattern
            $phpDataHandler->getResource()->asCode();
        }
        return $cfg_array;
    }

    /******************************************************************************************************************/

    public static function &getConfiguration($context=NULL)
    {
        if(isset($ptr))unset($ptr);
        if(!isset($context))
        {
            $context = get_called_class();
            if(!isset(self::$sysConfig[$context]))self::$sysConfig[$context]=[];
            $ptr = &self::$sysConfig[$context];
        }
        else
        {
            $ptr = &self::$sysConfig;
            $params = func_get_args();
            foreach($params as $param)$ptr=&$ptr[$param];
        }
        return $ptr;
    }

    public static function getDirectory($dirName)
    {
        if(!in_array($dirName, array_keys(self::$DIRECTORY)))throw new \Exception("Repertoire \"".$dirName."\" inexistant");
        return self::$DIRECTORY[$dirName];
    }

    public static function getHttpRequest()
    {
        die("<fieldset><legend>die in ".__FILE__." @ line ".__LINE__."</legend><pre>".print_r([], TRUE)."</pre></fieldset>");
        $httpRequest = Oculie\Factory\HttpRequest::create();
        //$httpRequest = new Oculie\DTO\Request\Http();
        return $httpRequest;
    }

    public static function getInfo()
    {
        echo "<p>Affichage des informations systeme ...</p>";
    }

    public static function &getSystemConfiguration()//TODO: utiliser getConfiguration
    {
        return self::$sysConfig;
    }

    public static function &getRegister($registerName)
    {
        return self::$register[$registerName];
    }

    public static function &getRegisteredController($context=NULL)
    {
        if(!isset($context))$context=get_called_class();
        return self::$sysConfig["CONTROLLER"][$context];
    }

    public static function getRegisteredClass($alias)
    {
        return self::$registered_class[$alias];
    }

    public static function handle($class)
    {
        if(!class_exists($class))throw new Exception("Tentative de fonctionner avec une classe inexistante : \"".$class."\"");//TODO: gerer les exception en cascade -> ce message ne s'affiche pas par ecraser par le throw
        call_user_func_array([$class, "setCore"], [self::$core]);
    }

    public static function handleException(Exception $e)
    {
        echo"<pre>";var_dump($e);echo"</pre>";
    }

    public static function init()
    {
        spl_autoload_register(function($class)
        {
            $autoload_register = Oculie::get_autoload_register("CLASS");
            if(isset($autoload_register[$class])) require_once($autoload_register[$class]);
        });

        foreach(Oculie::get_autoload_register("FUNCTION") as $name => $file)
        {
            include_once($file);
        }

        //TODO: inserer self::start
    }

    public static function init_error_handler($callable=NULL)
    {
        $exceptionHandler = NULL;
        if(!isset($exceptionHandler)&&isset(self::$sysConfig["HANDLER"]["EXCEPTION"])&&(class_exists(self::$sysConfig["HANDLER"]["EXCEPTION"])||method_exists(self::$sysConfig["HANDLER"]["EXCEPTION"], "handle")))
        {
            $exceptionHandler = self::$sysConfig["HANDLER"]["EXCEPTION"];
        }
        if(isset($exceptionHandler))set_exception_handler(self::$sysConfig["HANDLER"]["EXCEPTION"]."::handle");
        else;//TODO: setter un defaulthandler

        $errorHandler = NULL;
        if(!isset($errorHandler)&&isset(self::$sysConfig["HANDLER"]["ERROR"])&&(class_exists(self::$sysConfig["HANDLER"]["ERROR"])||method_exists(self::$sysConfig["HANDLER"]["ERROR"], "handle")))
        {
            $errorHandler = self::$sysConfig["HANDLER"]["ERROR"];
        }
        if(isset($exceptionHandler))set_error_handler(self::$sysConfig["HANDLER"]["ERROR"]."::handle");
        else;

        //ini_set( "display_errors", "off" );
        //error_reporting( E_ALL );
    }

    public static function load($sourceFile)
    {
        if(file_exists($sourceFile))include($sourceFile);
    }

    public static function obj($subject)
    {
        $service_interface = NULL;
        if(is_string($subject))
        {
            if(isset(self::$registered_class[$subject]))
            {
                self::$subject = $subject;
                //die("<fieldset><legend>die in ".__FILE__." @ line ".__LINE__."</legend><pre>".print_r([self::$subject, self::$registered_class], TRUE)."</pre></fieldset>");
                if(!isset(self::$service_interface["Pattern"]))
                {
                    self::$service_interface["Pattern"] = new class () extends Oculie
                    {
                        public function create()
                        {
                            $factory = [self::getRegisteredClass(self::$subject)["factory"], "create"];
                            $instance = call_user_func_array($factory, []);
                            return $instance;
                        }
                    };
                }
                $service_interface = self::$service_interface["Pattern"];
            }
        }else throw new Exception("NIY in ".__FILE__." @ line ".__LINE__."");

        if(!isset($service_interface)) throw new Exception("Echec in ".__FILE__." @ line ".__LINE__."");

        return $service_interface;
    }

    public static function registerAction($name, $callable=NULL)
    {
        if(!isset(self::$sysConfig["ACTION"]))self::$sysConfig["ACTION"]=[];
        if(!isset($callable))
        {
            if(!is_callable($name)) new \Exception(__METHOD__." doit prendre un appel de fonction en parametre si unique");
            $callable = $name;
            $name = self::DEFAULT_ACTION_PREFIX.count(self::$sysConfig["ACTION"]);
        }
        if(!in_array($callable, self::$sysConfig["ACTION"]))
        {
            self::$sysConfig["ACTION"][$name] = $callable;
        }
        else
        {
            $name = "NAME TO DEFINE (must search in array) in ".__FILE__." @ line ".__LINE__;
        }
        return $name;
    }

    public static function useAppConfig($config=[])
    {
        if(!is_array($config))throw new Exception("La methode \"".__METHOD__."\" prend un tableau en parametre.");
        self::$appConfig = $config;
        foreach($config as $varName => $data)
        {
            switch(strtoupper($varName))
            {
                case self::APP_AUTOLOAD:
                    self::load_app_autoload($data);
                    break;
                case self::LOAD_RECURSIVE:
                    self::load_recursive($data);
                    break;
            }
        }
    }

    public static function useSysConfig($dataConfig=[])
    {
        if(is_array($dataConfig))
        {
            self::$sysConfig = $dataConfig;
        }
        elseif(is_string($dataConfig)&&file_exists($dataConfig))
        {
            self::$sysConfig = self::eval_conf($dataConfig);
        }
        else self::$sysConfig = [];
        //TODO: verifier les données de configuration
    }

    public static function set_autoload_register($param1=NULL, $param2=NULL, $autoload_register=[])
    {
        //TODO: inclure ailleurs
        /*
        include_once("Implementation/entity/Event/defaultSettingsMethods.php");
        */
        if(!isset($param2))
        {
            //TODO: tester $param1
            $autoload_register = $param1;
            if(is_string($autoload_register)&&file_exists($autoload_register))
            {
                $autoload_register = self::eval_conf($autoload_register);//TODO appeller eval conf a l'exterieur de la fonction
            }
            self::$autoload_register["CLASS"] = array_merge(self::$autoload_register["CLASS"], $autoload_register);
        }
        else
        {
            $autoload_type = $param1;//TODO: test $param1
            $autoload_register = $param2;
            self::$autoload_register["FUNCTION"] = array_merge(self::$autoload_register["CLASS"], $autoload_register);
        }
    }

    public static function set_autoload_register_function($autoload_register=[])
    {
        self::$autoload_register["FUNCTION"] = array_merge(self::$autoload_register["FUNCTION"], $autoload_register);
    }

    public static function setEnvMode($mode)
    {
        //TODO: DEV, RECETTE, PREPROD, PROD etc...
    }

    public static function start($action=NULL, $context=NULL)
    {
        //TODO: charger l'autoload si necessaire
        include_once("core/entity/Context.php");
        include_once("core/entity/Request.php");
        include_once("core/entity/Resource.php");
        include_once("core/handler/XmlDocument.php");
        include_once("core/interpreter/PhpHttpRequest.php");
        include_once("core/processor/Webpage.php");
        include_once("core/processor/Event.php");
        include_once("core/reader/Request.php");
        include_once("core/Register/MetaObject.php");
        include_once("default/Application.php");

        if(isset($action))
        {
            foreach(self::getConfiguration(self::$appConfig["SRC"]["MAIN_CLASS"])["CONTROLLER"] as $name => $controller)
            {
                if($controller["event-name"]==$action)
                {
                    call_user_func_array($controller["callback"][0], $controller["callback"][1]);
                    break;
                }
            }
        }
        else
        {
            //TODO; creer des handler par defaut au cas ou erreurs precoces
            if(isset(self::$sysConfig["AUTOLOAD"]["INI_FILE"])&&file_exists(self::$sysConfig["AUTOLOAD"]["INI_FILE"]))
            {
                self::set_autoload_register(self::$sysConfig["AUTOLOAD"]["INI_FILE"]);
            }

            //!
            $app_autoload = NULL;
            if(isset(self::$appConfig["AUTOLOAD"]["INI_FILE"])&&file_exists(self::$appConfig["AUTOLOAD"]["INI_FILE"]))
                $app_autoload = self::eval_conf(self::$appConfig["AUTOLOAD"]["INI_FILE"]);

            if(isset($app_autoload)) self::set_autoload_register($app_autoload);

            /*TODO: a effacer avec la suppression de self::start() inserer dans la methode self::init()
            spl_autoload_register(function($Pattern)
            {
                $autoload_register = Oculie::get_autoload_register();
                if(isset($autoload_register[$Pattern])) require_once($autoload_register[$Pattern]);
            });
            */

            self::init_error_handler();//TODO: parametrer le error_handler

            //!
            $classRegister = [\Oculie\Core\Register\MetaObject::class, "Register"];
            call_user_func_array($classRegister, [[]]);

            //!finally
            if(isset(self::$appConfig["MAIN_CLASS_UTILITY"])&&file_exists(self::$appConfig["MAIN_CLASS_UTILITY"]["filename"]))require_once(self::$appConfig["MAIN_CLASS_UTILITY"]["filename"]);

            //TODO: recuperer le contexte et le processeur en fonction du contexte -> cf Pattern Application (processor = handler)
            /*
            $processor = Oculie\Core\Processor\Webpage::Pattern;
            try
            {
                call_user_func_array([$processor, "execute"], []);
            }
            */

            try
            {
                //TODO: recuperer le contexte et le processeur en fonction du contexte
                $process = [Oculie\Processor\Webpage::class, "execute"];

                if(isset(self::$appConfig["MAIN"])&&file_exists(self::$appConfig["MAIN"]))include_once(self::$appConfig["MAIN"]);
                if(is_callable($callable = [self::$appConfig["MAIN_CLASS_UTILITY"]["name"], "execute"]))
                {
                    $instance = new self::$appConfig["MAIN_CLASS_UTILITY"]["name"]();
                    $callable = [$instance, "execute"];
                    call_user_func_array($callable, []);//TODO: resoudre la nature du callable dans la mesure ou il peut ne pas s'agir d'une classe
                }
                \Oculie\Processor\Event::start();

                call_user_func_array($process, []);
            }
            catch(Exception $e)
            {
                Oculie::handleException($e);
            }

            if(FALSE)//TODO: checker si une application est configurée. La condition du if est verifiée si oui
            {
                /*
                $contextTemplateDirectory = $parameters->rootDirectory;//TODO: nom des setter explicite
                */
            }
            else
            {
                $app = new \Oculie\Core\Application();
                $context = new Oculie\Entity\Context();//TODO: $context = self::getContext(); le context varie en fonction de la façon ou la methode est appellée
                $context->dataTemplateDirectory = self::DEFAULT["WEB"]["DATA_TEMPLATE_DIRECTORY"];
                $context->dataProcessorDirectory = self::DEFAULT["WEB"]["DATA_PROCESSOR_DIRECTORY"];
                $app->setContext($context);
                $phpHttpRequest = \Oculie\Reader\HttpRequest::read()->getPhpHttpRequest();//un Reader peut avoir null en parametre d'entree
                $request = \Oculie\Interpreter\PhpHttpRequest::parse($phpHttpRequest)->getRequest();//un parseur peut avoir un texte code autre qur php en parametre
                $app->execute($request);
            }
        }
    }

    public static function dump_conf()
    {
        die("<fieldset><legend>".__METHOD__."</legend><pre>".print_r(self::$sysConfig, TRUE)."</pre></fieldset>");
    }


    //!------------------------------------------------------------------------------------------------------------------------------------------------------------
    private static function eval_conf($cfg_array_file)
    {
        $srcCode = trim(file_get_contents($cfg_array_file));
        $srcCode = str_replace("<?php", "", $srcCode);
        $srcCode = str_replace("?>", "", $srcCode);

        $cfg_array = eval("return ".$srcCode.";");

        return $cfg_array;
    }

    private static function load_app_autoload($autoload_file)
    {
        if(is_string($autoload_file) && file_exists($autoload_file)) require_once($autoload_file);
        elseif(is_array($autoload_file))
        {
            foreach($autoload_file as $file) if(file_exists($file)) require_once($file);//TODO: warning ou expcetion pour les fichiers non-existant.
        }
        else throw new Exception("Erreur lord de la configuration de l'autoloader");
    }

    private static function load_recursive($requiredFile)
    {
        $directory = [];
        if(!is_array($requiredFile)) $requiredFile = [$requiredFile];
        foreach($requiredFile as $resourceName)
        {
            if(!is_string($resourceName) && !file_exists($resourceName)) throw new Exception("La ressource \"".(is_string($resourceName) ? $resourceName : print_r($resourceName, TRUE))."\" n'est pas un nom de fichier.");
            if(is_dir($resourceName))
            {
                $directory[] = $resourceName;
                while(!empty($directory))
                {
                    $resourceName = scandir($directory[0]);
                    foreach($resourceName as $fileName)
                    {
                        if(in_array($fileName, [".", ".."]))continue;
                        if(is_dir($directory[0]."/".$fileName)) $directory[] = $directory[0]."/".$fileName;
                        else require_once($directory[0]."/".$fileName);
                    }
                    array_shift($directory);
                }
            }
            else require_once($resourceName);
        }
    }
}