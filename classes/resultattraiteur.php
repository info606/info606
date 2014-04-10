<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once($rootPath."/info606/outils_php/autoload.php");

class ResultatTraiteur extends Traiteur
{
	private $composanteM;
	private $etudiantM;
	private $validationM;
	
	public function __construct()
	{
		$this->csvLoader = null;
		$this->path = $_SERVER['DOCUMENT_ROOT']."/donnees/resultats/"
	}

	public function ajout(String $filename)
	{
		$this->csvLoader = new CSVLoader($this->path.$filename, ";");
		$compo
	}

	public function suppression(String $filename);
	public function maj();
}