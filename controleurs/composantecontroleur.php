<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class ComposanteControleur extends Controleur
{
	public $composanteManager;

	public function __construct()
	{
		parent::__construct();
		$this->composanteManager = new ComposanteManager($this->pdo);
	}
}