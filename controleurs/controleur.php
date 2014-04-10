<?php

	$rootPath = $_SERVER['DOCUMENT_ROOT'];

	require_once($rootPath."/info606/outils_php/connexion.pdo.class.php");

	class Controleur 
	{
	    protected $pdo;

	    public function __construct() 
	    {
	        myPDO::parametres('mysql:host=127.0.0.1;dbname=mario52_fr', 'root', '') ;
	        $this->pdo = myPDO::donneInstance();
	  	}
	}

?>