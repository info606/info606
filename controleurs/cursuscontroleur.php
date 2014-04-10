<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class CursusControleur extends Controleur
{
	public $cursusManager;

	public function __construct()
	{
		parent::__construct();
		$this->cursusManager = new CursusManager($this->pdo);
	}
}