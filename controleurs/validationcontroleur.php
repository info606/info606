<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class ValidationControleur extends Controleur
{
	public $validationManager;

	public function __construct()
	{
		parent::__construct();
		$this->validationManager = new ValidationManager($this->pdo);
	}
}