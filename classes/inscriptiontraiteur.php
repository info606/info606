<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once($rootPath."/info606/outils_php/autoload.php");
require_once($rootPath."/info606/outils_php/arrayTools.php");
require_once($rootPath."/info606/outils_php/stringTools.php");

class InscriptionTraiteur extends Traiteur
{
	private $composanteM;
	private $etudiantM;
	private $validationM;
	private $epreuveM;
	private $etapeM;
	private $epreuvesLib = array("D1P", "D1T", "D2P", "D2T", "D3P", "D3T", "D4P", "D4T", "D5P", "D5T",);
	private $titres = array("Code Etudiant","Nom", "prénom", "login", "mail", "date de naissance","Composante (code)", "composante (lib.)", "Etape (code)", "IAE (date)");
	
	public function __construct()
	{
		$this->csvLoader = null;
		$this->path = $_SERVER['DOCUMENT_ROOT']."/info606/donnees/etudiants/";

		$composanteC = new ComposanteControleur();
		$etudiantC = new EtudiantControleur();
		$validationC = new ValidationControleur();
		$epreuveC = new EpreuveControleur();
		$etapeC = new EtapeControleur();

		$this->composanteM = $composanteC->composanteManager;
		$this->etudiantM = $etudiantC->etudiantManager;
		$this->validationM = $validationC->validationManager;
		$this->epreuveM = $epreuveC->epreuveManager;
		$this->etapeM = $etapeC->etapeManager;
	}

	public function traiter($filename, $definitif=false)
	{
		try{
			$this->csvLoader = new CSVLoader($this->path.$filename, $this->titres, ";");
		}
		catch(Expression $e)
		{
			$_SESSION['erreurs'][] = $e->getMessage();
			exit();
		}

		$indexCodComp = $this->csvLoader->getIndexTitle(array("composante","code"));
		$indexLibComp = $this->csvLoader->getIndexTitle(array("composante","lib"));
		$indexCodEtape = $this->csvLoader->getIndexTitle(array("etape","code"));
		$indexNumEtud = $this->csvLoader->getIndexTitle(array("etudiant","code"));
		$indexNomEtud = $this->csvLoader->getIndexTitle(array("individu","nom")); /* Attention, il faut que la colonne nom soit avant la colonne prénom */
		$indexPrenomEtud = $this->csvLoader->getIndexTitle(array("prénom"));
		$indexLoginEtud = $this->csvLoader->getIndexTitle(array("login"));
		$indexMailEtud = $this->csvLoader->getIndexTitle(array("mail"));
		$indexDateNais = $this->csvLoader->getIndexTitle(array("date","naissance"));
		$indexDateIAE = $this->csvLoader->getIndexTitle(array("date","iae"));
		$indexDateC2I = $this->csvLoader->getIndexTitle(array("date","C2i"));
		$indexNumRegime = $this->csvLoader->getIndexTitle(array("régime", "code"));

		$data = $this->csvLoader->getData();
		foreach ($data as $ligne) {
			/* Récupérer Composante */
			$composante = new Composante();
			$composante->codeComposante = $ligne[$indexCodComp];
			$composante->libComposante = $ligne[$indexLibComp];
			/* Insérer Composante */
			$this->composanteM->ajouter($composante);
			try{
				$composante->numComposante = $this->composanteM->recupererNum($composante);
			}
			catch(Exception $e)
			{
				$_SESSION['erreurs'][] = "Il n'existe aucune composante correspondante pour l'étudiant : ".$ligne[$indexNumEtud]." - Composante : ".$composante->libComposante;
				continue;
			}

			/* Récupérer Etape */
			$etape = new Etape();
			$etape->codeEtape = $ligne[$indexCodEtape];
			$etape->numComposante = $composante->numComposante;
			try{
				$etape->idEtape = $this->etapeM->recupererNum($etape);
			}
			catch(Exception $e)
			{
				$_SESSION['erreurs'][] = "Il n'existe aucune étape correspondante pour l'étudiant : ".$ligne[$indexNumEtud]." - Code étape : ".$etape->codeEtape." - Composante : ".$composante->libComposante;
				continue;
			}

			$etudiant = new Etudiant();
			/* Récupérer Etudiant */
			$etudiant->numEtudiant = $ligne[$indexNumEtud];
			$etudiant->nomEtudiant = $ligne[$indexNomEtud];
			$etudiant->prenomEtudiant = $ligne[$indexPrenomEtud];
			$etudiant->loginEtudiant = $ligne[$indexLoginEtud];
			$etudiant->mailEtudiant = $ligne[$indexMailEtud];
			$etudiant->dateNaisEtudiant = dateToMySQL($ligne[$indexDateNais]);
			$etudiant->dateIAEEtudiant = dateToMySQL($ligne[$indexDateIAE]);
			$etudiant->dateIAC2IEtudiant = dateToMySQL($ligne[$indexDateC2I]);
			$etudiant->C2IValide = 0;
			$etudiant->numRegime = $ligne[$indexNumRegime];
			$etudiant->idEtape = $etape->idEtape;
			$etudiant->mdpEtudiant = dateToPassword($ligne[$indexDateNais]);
			/* Insérer Etudiant */
			$this->etudiantM->ajouter($etudiant);

			
			foreach ($this->epreuvesLib as $value) {
				$epreuve = new Epreuve();
				$epreuve->numComposante = $composante->numComposante;
				$epreuve->libEpreuve = $value;

				/* Insérer Epreuve pour la composante */
				$this->epreuveM->ajouter($epreuve);
				$epreuve->idEpreuve = $this->epreuveM->recupererNum($epreuve);
				
				/* Insérer Validation à -1 pour l'étudiant et pour chaque épreuve de la composante */
				$validation = new Validation();
				$validation->numEnseignant = $_SESSION['numEnseignant'];
				$validation->numEtudiant = $etudiant->numEtudiant;
				$validation->idEpreuve = $epreuve->idEpreuve;
				$validation->valeurValidation = -1;
				$this->majValidation($validation, $_SESSION['admin']);
			}
		}
	}

	private function majValidation($validation, $admin)
	{
		if($this->validationM->exists($validation))
		{
			$idValid = $this->validationM->recupererNum($validation);
			/* On récupère l'ancienne valeur de la validation */
			$vRecup = $this->validationM->recupererParNum($idValid);

			/* Cas où la valeur rétrograderait */
			if($vRecup->valeurValidation > $validation->valeurValidation && !$admin)
			{
				throw new Exception("Vous n'avez pas les droits nécessaires pour rétrograder une validation");
			}
			else
			{
				$validation->idValidation = $idValid;
				$this->validationM->maj($validation);
			}
		}
		else
		{
			$this->validationM->ajouter($validation);
		}
	}

	public function suppression($filename)
	{
		if(!unlink($this->path.$filename))
		{
			$_SESSION['erreurs'][] = "Impossible de supprimer le fichier ".$this->path.$filename ;
		}
		else
		{
			$this->maj();
		}
	}

	public function maj()
	{
		/* Récupérer tous les fichiers */
		$files = scandir($this->path, SCANDIR_SORT_NONE);
		var_dump($files);
		foreach ($files as $key => $value) {
			if(!is_file($this->path.$value))
			{
				unset($files[$key]);
			}
		}
		var_dump($files);
		/* Les trier par ordre chronologique */
		usort($files,'arrayDateSort');
		var_dump($files);
		/* Les traiter un par un */
		foreach ($files as $value) {
			$this->traiter($value);
		}
	}

}