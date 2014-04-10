<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class EpreuveControleur extends Controleur
{
	public $epreuveManager;

	public function __construct()
	{
		parent::__construct();
		$this->epreuveManager = new EpreuveManager($this->pdo);
	}
}