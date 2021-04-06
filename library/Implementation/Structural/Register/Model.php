<?php
namespace Oculie\Core\Register;

//use Oculie\Core\Builder\Repository;

class Model extends \Oculie
{
    /*
     * Behavior
     */

    //!Databases management

    public static function createDatabase($name)
    {
        self::$database[$name] = new \Oculie\Core\Builder\DbConnection();
        return self::$database[$name];
    }

    public static function setDatabase($name, $instance)
    {
        self::$database[$name] = $instance;
    }

    public static function getDatabase($name)//TODO a mettre dans classe parente
    {
        if(isset(self::$database[$name]) && get_class(self::$database[$name])==\Oculie\Core\Builder\DbConnection::class)
        {
            self::createDatabaseHandler($name);
        }
        return isset(self::$database[$name]) ? self::$database[$name] : NULL;
    }

    public static function getDatabases()
    {
        return self::$database;
    }

    public static function updateRepositories()
    {
        foreach(self::$repository as $name => $repository)
        {
            if(is_subclass_of($repository, \Oculie\Core\Builder::class))
            {
                $repository = $repository->getInstance();
                //\Oculie\DEbug::dump(["name"=>$name, "database"=>get_class(self::$handler["database"]), ($repository->getName())], FALSE);
                if(!self::$handler["database"]->getRepository($repository->getName()))
                {
                    self::$handler["database"]->createRepository($repository);
                }
            }
            self::$repository[$name] = $repository;
        }
    }

    /*
     * Variables & Procedures
     */

    //TOFO passer par des instance de handler unique pour modifier les objets database/repo
    protected static $handler       = [];
    protected static $database      = [];
    protected static $repository    = [];

    protected static function createRepositoryHandledResource($repository, $dbConn)
    {
        $hResource = new class()
        {
            public $dbConn;
            public $repository;
            public function getDbConnection(){return $this->dbConn;}
            public function getRepository(){return $this->repository;}
        };
        $hResource->repository = $repository;
        $hResource->dbConn = $dbConn;
        return $hResource;
    }

    protected static function createDatabaseHandler($name)
    {
        if(!isset(self::$handler["database"]))
        {
            $dbConn = self::$database[$name]->getInstance();
            $label = array_combine(["vendor", "engine"], explode("::",$dbConn->getDriver()));
            $handler = self::getExtension($label["vendor"])->getClass(\Oculie\Core\Handler\Database::class);

            $databaseBuilder = self::getExtension($label["vendor"])->getClass(\Oculie\Core\Builder\Database::class);
            $databaseBuilder = new $databaseBuilder();
            $databaseBuilder->setDataConnection($dbConn);
            $database = $databaseBuilder->getInstance();

            self::$handler["database"] = new $handler();
            self::$handler["database"]->handle($database->getConnectionObject());
        }

        self::$database[$name] = new class($name) extends \Oculie\Core\Register\Model
        {
            public function __construct($name)
            {
                $this->dbName = $name;
                $dbConn = self::$database[$name]->getInstance();
                $this->driver = $dbConn->getDriver();
                $this->label = array_combine(["vendor", "engine"], explode("::",$dbConn->getDriver()));

                /*
                $handler = $subject->getHandler();
                $this->tmpHandler = new $handler();
                $this->tmpHandler->handle($subject->getConnectionObject());
                */
            }

            //public function getConnectionObject(){return $this->tmpHandler->getSubject();}
            public function getDriver(){return $this->driver;}

            public function createRepository($name)
            {
                $repoName = $this->dbName."\\".$name;
                if(isset(self::$repository[$repoName]))
                {
                    $exceptionMsg = "Echec de creation d'un depot: depot \"".$name."\" déjà existant";
                    throw new \Exception($exceptionMsg);
                }

                $repositoryBuilder = self::getExtension($this->label["vendor"])->getClass(\Oculie\Core\Builder\Repository::class);
                self::$repository[$repoName] = new $repositoryBuilder();
                self::$repository[$repoName]->setName($name);
                return self::$repository[$repoName];
            }

            public function getRepository($name)
            {
                $repoName = $this->dbName."\\".$name;
                if(isset(self::$repository[$repoName]) && is_subclass_of(self::$repository[$repoName], \Oculie\Core\Builder::class))
                {
                    self::$repository[$repoName] = self::$repository[$repoName]->getInstance();
                    \Oculie\Debug::dump([
                        "instance"  => self::$repository[$repoName]
                    ], FALSE);
                    self::$handler["database"]->create(self::$repository[$repoName]);
                    //self::createRepositoryHandler($repoName);
                }
                return isset(self::$repository[$repoName]) ? self::$repository[$repoName] : NULL;
            }

            public function getRepositories()
            {
                foreach(self::$repository as $name => $repository)
                {
                    $this->getRepository($name);
                }
                return self::$repository;
            }

            /*
             *
             */

            private $dbName;
            private $label;
            private $driver;
            private $tmpHandler;
            private $dbConn;
        };
    }

    protected static function createRepositoryHandler($name)
    {
        $label = array_combine(["database", "repository"], explode("\\", $name));

        $repository = self::$repository[$name]->getInstance();
        //$dbConn = self::$database[$label["database"]]->getConnectionObject();
        //$hResource = self::createRepositoryHandledResource($repository, $dbConn);

        self::$repository[$name] = new class ($name) extends \Oculie\Core\Register\Model
        {
            public function __construct($name)
            {
                $label = array_combine(["database", "repository"], explode("\\", $name));
                $driver = array_combine(["vendor", "engine"], explode("::", self::$database[$label["database"]]->getDriver()));
                $handler = self::getExtension($driver["vendor"])->getClass(\Oculie\Core\Handler\Repository::class);
                $this->tmpHandler = new $handler();
            }

            public function handle($subject)
            {
                \Oculie\Debug::dump(["on manipule l'objet \"".get_class($subject)."\""], FALSE);
                $this->tmpHandler->handle($subject);
            }

            protected $tmpHandler;
        };

        //self::$repository[$name]->handle($hResource);
        \Oculie\Debug::dump([
            "current Pattern" => get_class(self::$repository[$name])
        ], FALSE);
    }
}