<?php
namespace Oculie\Core;

class Repository1
{
	private static $connexion = [];
	
	public function getDao($name)
	{
		if(!isset(self::$connexion[$name])) throw new Exception("Connexion \"".$name."\" inexistante");
		return self::$connexion[$name];
	}
	
	public static function create($className=NULL)
	{
		echo "<p>creation de la classe \"".$className."\"</p>";
		return new $className();
	}
	
	public static function getLastInsertId($dbh)
	{
		return $dbh->lastInsertId();
	}
	
	public static function save($instance, $dbh)
	{
		echo "<p>Sauvegarde d'un l'objet ".get_class($instance)."</p>";
		
		$sql = "SELECT table_name FROM information_schema.tables WHERE table_schema = :schema_name AND table_name = :table_name";
		$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$sth->execute(array(":schema_name"=>self::$schema["dashboard"]["name"], "table_name"=>"Person"));
		$response = $sth->fetchAll();
		
		echo "<p>instantiation de l'objet ".print_r($response, TRUE)."</p>";
		
		if(empty($response))
		{
			echo "<p>Creation de la table</p>";
			$tableName = get_class($instance);
			$sql = "CREATE TABLE %s (";
			$sql.= "`id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,";
			$sql.= "`firstName` VARCHAR(30) NOT NULL,";
			$sql.= "`lastName` VARCHAR(30) NOT NULL,";
			$sql.= "`age` int(2)";
			$sql.= ");";
			/*
			CREATE TABLE MyGuests (
				id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				firstname VARCHAR(30) NOT NULL,
				lastname VARCHAR(30) NOT NULL,
				email VARCHAR(50),
				reg_date TIMESTAMP
				)
			*/
			//$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$success = $dbh->exec(sprintf($sql, $tableName));
			if($success !== FALSE) echo "<p>Reussite de la creation de la table</p>";
			else die("<p>echec lors de la creation de la table</p><p>".sprintf($sql, $tableName)."</p>");
		}
		
		if(TRUE)//La donnée n'existe pas en database
		{
			echo "<p>insertion de la donnée en bdd</p>";
			$sql = sprintf("INSERT INTO %s (firstName, lastName, age) VALUES (:firstName, :lastName, :age)", get_class($instance));
			$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$success = $sth->execute([":firstName"=>$instance->firstName, ":lastName"=>$instance->lastName, ":age"=>$instance->age]);
			if(!$success)
			{
				die("<p>Echec lors de l'insertion de valeur</p>");
			}
		}
		
		return $response;
	}
	
	public static function setDao($name, $dao)
	{
		self::$connexion[$name] = $dao;
	}
}
?>
