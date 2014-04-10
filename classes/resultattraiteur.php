<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once($rootPath."/info606/outils_php/autoload.php");

class ResultatTraiteur extends Traiteur
{
	private $composanteM;
	private $etudiantM;
	private $validationM;
	private $enseignantM;
	
	public function __construct()
	{
		$this->csvLoader = null;
		$this->path = $_SERVER['DOCUMENT_ROOT']."/info606/donnees/resultats/";
		$composanteC = new ComposanteControleur();
		$etudiantC = new EtudiantControleur();
		$validationC = new ValidationControleur();
		$enseignantC = new EnseignantControleur();
		$this->composanteM = $composanteC->composanteManager;
		$this->etudiantM = $etudiantC->etudiantManager;
		$this->validationM = $validationC->validationManager;
		$this->enseignantM = $enseignantC->enseignantManager;
	}

	public function ajout($filename)
	{
		$this->csvLoader = new CSVLoader($this->path.$filename, ";");
		$c = new Composante();
		$v = new Validation();
		$et = new Etudiant();
		$ens = new Enseignant();

		$data = $this->csvLoader->getData();

		foreach ($data as $ligne) {
			/* Récupération du numéro de la composante */
			$index = $this->csvLoader->getIndexTitle(array("composante"));
			//$c->libComposante = $ligne[$index];
			print_r($ligne);
			break;
		}
		

	}

	public function suppression($filename)
	{
		echo "Suppression";
	}

	public function maj()
	{
		echo "Maj";
	}
}