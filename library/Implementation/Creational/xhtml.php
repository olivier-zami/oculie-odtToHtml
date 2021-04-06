<?php
namespace Oculie\Core\DAO;

class Xhtml extends \Oculie\Core\DAO //TODO: diagnostiquer les oublie de namepace ie ... extends DAO pour la generation de message d'erreur
{	
    const parameters = ["db:engine", "db:name", "db:host", "db:port", "user:name", "user:pass"];
	
	private $dao;
	
	public function __construct(array $parameters=[])
	{
	}
	
	public function getDao()
	{
		return $this->dao;
	}
}
?>