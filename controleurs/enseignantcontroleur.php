<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class EnseignantControleur extends Controleur
{
	public $enseignantManager;

	public function __construct()
	{
		parent::__construct();
		$this->enseignantManager = new EnseignantManager($this->pdo);
	}
}