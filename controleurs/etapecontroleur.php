<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class EtapeControleur extends Controleur
{
	public $etapeManager;

	public function __construct()
	{
		parent::__construct();
		$this->etapeManager = new EtapeManager($this->pdo);
	}
}