<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class EtudiantControleur extends Controleur
{
	public $etudiantManager;

	public function __construct()
	{
		parent::__construct();
		$this->etudiantManager = new EtudiantManager($this->pdo);
	}
}