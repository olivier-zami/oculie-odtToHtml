<?php
namespace Oculie\Controller\Dao;

class PDO
{
	const parameters = ["db:engine", "db:name", "db:host", "db:port", "user:name", "user:pass"];
	
	private $dao;
	
	public function __construct($parameters=[])
	{
		if(!isset($parameters["db:port"])) $parameters["db:port"] = "3306";
		
		foreach(self::parameters as $pName)
		{
			if(!isset($parameters[$pName])) throw new \Exception("parametre \"".$pName."\" manquant");
		}
	
		$dsn = $parameters["db:engine"].":dbname=".$parameters["db:name"].";host=".$parameters["db:host"].";".$parameters["db:port"];
		$user = $parameters["user:name"];
		$pass = $parameters["user:pass"];
		
		try
		{
			$this->dao = new \PDO($dsn, $user, $pass);
			$this->dao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(\PDOException $e)
		{
			//throw new Exception('Connection failed: ' . $e->getMessage());
		}
	}
	
	public function getDao()
	{
		return $this->dao;
	}
}
?>
