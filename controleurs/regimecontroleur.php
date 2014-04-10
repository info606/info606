<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class RegimeControleur extends Controleur
{
	public $regimeManager;

	public function __construct()
	{
		parent::__construct();
		$this->regimeManager = new RegimeManager($this->pdo);
	}
}