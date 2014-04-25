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
	private $cursusM;
	private $epreuvesLib = array("D1P", "D1T", "D2P", "D2T", "D3P", "D3T", "D4P", "D4T", "D5P", "D5T",);
	private $titres = array("Code Etudiant",
							"Nom",
							"prénom", 
							"login", 
							"mail", 
							"date de naissance",
							"Composante (code)", 
							"composante (lib.)", 
							"Etape (code)",
							"Etape (version)",
							"IAE (date)",
							"Etape (version)",
							"cursus (code)",
							"niveau",
							"Date IA C2i",
							"régime (code)",
							"régime (lib.)"
							);
	
	public function __construct()
	{
		$this->csvLoader = null;
		$this->path = $_SERVER['DOCUMENT_ROOT']."/info606/donnees/etudiants/";

		$composanteC = new ComposanteControleur();
		$etudiantC = new EtudiantControleur();
		$validationC = new ValidationControleur();
		$epreuveC = new EpreuveControleur();
		$etapeC = new EtapeControleur();
		$cursusC = new CursusControleur();

		$this->composanteM = $composanteC->composanteManager;
		$this->etudiantM = $etudiantC->etudiantManager;
		$this->validationM = $validationC->validationManager;
		$this->epreuveM = $epreuveC->epreuveManager;
		$this->etapeM = $etapeC->etapeManager;
		$this->cursusM = $cursusC->cursusManager;
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
		$indexVersionEtape = $this->csvLoader->getIndexTitle(array("etape","version"));
		$indexCodCursus = $this->csvLoader->getIndexTitle(array("cursus","code"));
		$indexNiveau = $this->csvLoader->getIndexTitle(array("niveau"));
		$indexNumEtud = $this->csvLoader->getIndexTitle(array("etudiant","code"));
		$indexNomEtud = $this->csvLoader->getIndexTitle(array("individu","nom")); /* Attention, il faut que la colonne nom soit avant la colonne prénom */
		$indexPrenomEtud = $this->csvLoader->getIndexTitle(array("prénom"));
		$indexLoginEtud = $this->csvLoader->getIndexTitle(array("login"));
		$indexMailEtud = $this->csvLoader->getIndexTitle(array("mail"));
		$indexDateNais = $this->csvLoader->getIndexTitle(array("date","naissance"));
		$indexDateIAE = $this->csvLoader->getIndexTitle(array("date","iae"));
		$indexDateC2I = $this->csvLoader->getIndexTitle(array("date","C2i"));
		$indexNumRegime = $this->csvLoader->getIndexTitle(array("régime", "code"));
		$indexLibRegime = $this->csvLoader->getIndexTitle(array("régime", "lib"));

		$data = $this->csvLoader->getData();
		
		foreach ($data as $ligne) {
			$importErr = "Impossible d'importer la ligne : ";
			/* Récupérer Composante */
			if(!array_key_exists($indexNumEtud, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le numéro de l'étudiant.";
				continue;
			}
			$importErr .= "Etudiant n°".$ligne[$indexNumEtud]." : ";
			if(!array_key_exists($indexCodComp, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le code de la composante.";
				continue;
			}
			if(!array_key_exists($indexLibComp, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le libellé de la composante.";
				continue;
			}
			if(!array_key_exists($indexCodEtape, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le code de l'étape.";
				continue;
			}
			if(!array_key_exists($indexNomEtud, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le nom de l'étudiant.";
				continue;
			}
			if(!array_key_exists($indexPrenomEtud, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le prénom de l'étudiant.";
				continue;
			}
			if(!array_key_exists($indexLoginEtud, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le login de l'étudiant.";
				continue;
			}
			if(!array_key_exists($indexMailEtud, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le mail de l'étudiant.";
				continue;
			}
			if(!array_key_exists($indexDateNais, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque la date de naissance de l'étudiant.";
				continue;
			}
			if(!array_key_exists($indexDateIAE, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque la date d'inscription administrative.";
				continue;
			}
			if(!array_key_exists($indexDateC2I, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque la date d'inscription au C2i.";
				continue;
			}
			if(!array_key_exists($indexNumRegime, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le code de régime.";
				continue;
			}
			if(!array_key_exists($indexLibRegime, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le libellé du régime.";
				continue;
			}
			if(!array_key_exists($indexCodCursus, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le code du cursus.";
				continue;
			}
			if(!array_key_exists($indexNiveau, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le niveau dans le diplôme.";
				continue;
			}
			if(!array_key_exists($indexVersionEtape, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque la version de l'étape.";
				continue;
			}

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

			/* Récupérer Cursus */
			$cursus = new Cursus();
			$cursus->codeCursus = $ligne[$indexCodCursus];
			$cursus->niveau = $ligne[$indexNiveau];
			try{
				$cursus->idCursus = $this->cursusM->recupererNum($cursus);
			}
			catch(Exception $e)
			{
				$_SESSION['erreurs'][] = "Il n'existe aucun cursus correspondant pour l'étudiant : ".$ligne[$indexNumEtud]." - Code cursus: ".$cursus->codeCursus." - Composante : ".$composante->libComposante;
				continue;
			}

			/* Récupérer Etape */
			$etape = new Etape();
			$etape->codeEtape = $ligne[$indexCodEtape];
			$etape->numComposante = $composante->numComposante;
			$etape->versionEtape = $ligne[$indexVersionEtape];
			$etape->idCursus = $cursus->idCursus;
			var_dump($etape);
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
			if($this->etudiantM->exists($etudiant))
			{
				$this->etudiantM->maj($etudiant);
			}
			else
			{
				$this->etudiantM->ajouter($etudiant);
			}
			

			
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