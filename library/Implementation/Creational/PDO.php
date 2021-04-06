<?php
namespace Oculie\Core\DAO;

class PDO extends \Oculie\Core\DAO //TODO: diagnostiquer les oublie de namepace ie ... extends DAO pour la generation de message d'erreur
{
    const parameters = ["db:engine", "db:name", "db:host", "db:port", "user:name", "user:pass"];
	
	private $dao;
	
	public function __construct(array $parameters=[])
	{
		if(!isset($parameters["db:port"])) $parameters["db:port"] = "3306";
		foreach(self::parameters as $pName)
		{
			if(!isset($parameters[$pName]) && !in_array($pName, ["db:name"])) throw new \Exception("parametre \"".$pName."\" manquant");
			elseif(!isset($parameters[$pName])) $parameters[$pName] = "";
			else;
		}
		
		$dsn = $parameters["db:engine"].":dbname=".$parameters["db:name"].";host=".$parameters["db:host"].";".$parameters["db:port"];
		$user = $parameters["user:name"];
		$pass = $parameters["user:pass"];
		
		try
		{
			$this->dao = new \PDO($dsn, $user, $pass);
		}
		catch(PDOException $e)
		{
            self::getCore();//TODO: SQLSTATE[HY000] [1049] database info -> si info disponible dans conf/core et mode autogenerate on -> creation de la table
			throw new \Exception('Connection failed: ' .print_r($e->getCode(), TRUE)."<br/>". print_r(get_class_methods($e),TRUE)."<br/>".$e->getMessage());
		}
	}
	
	public function getDao()
	{
        echo "<p>GET DAO ....</p>";
		return $this->dao;
	}
}
?>

