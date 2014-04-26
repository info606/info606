<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once($rootPath."/info606/outils_php/autoload.php");
require_once($rootPath."/info606/outils_php/arrayTools.php");

class ResultatTraiteur extends Traiteur
{
	private $composanteM;
	private $etudiantM;
	private $validationM;
	private $epreuveM;
	private $epreuvesLib = array("D1P", "D1T", "D2P", "D2T", "D3P", "D3T", "D4P", "D4T", "D5P", "D5T",);
	private $titres = array("Composante (code)","Code Etudiant");
	
	public function __construct()
	{
		$this->csvLoader = null;
		$this->path = $_SERVER['DOCUMENT_ROOT']."/info606/donnees/resultats/";

		$composanteC = new ComposanteControleur();
		$etudiantC = new EtudiantControleur();
		$validationC = new ValidationControleur();
		$epreuveC = new EpreuveControleur();

		$this->composanteM = $composanteC->composanteManager;
		$this->etudiantM = $etudiantC->etudiantManager;
		$this->validationM = $validationC->validationManager;
		$this->epreuveM = $epreuveC->epreuveManager;
	}

	public function traiter($filename, $definitif=false)
	{
		try{
			$this->csvLoader = new CSVLoader($this->path.$filename, array_merge($this->titres,$this->epreuvesLib), ";");
		}
		catch(Exception $e)
		{
			$_SESSION['erreurs'][] = $e->getMessage();
			return;
		}

		$v = new Validation();

		$data = $this->csvLoader->getData();
		$indexCodComposante = $this->csvLoader->getIndexTitle(array("composante", "code"));
		$indexNumEtudiant = $this->csvLoader->getIndexTitle(array("etudiant", "code"));
		$indexD1P = $this->csvLoader->getIndexTitle(array("D1P"));
		$indexD2P = $this->csvLoader->getIndexTitle(array("D2P"));
		$indexD3P = $this->csvLoader->getIndexTitle(array("D3P"));
		$indexD4P = $this->csvLoader->getIndexTitle(array("D4P"));
		$indexD5P = $this->csvLoader->getIndexTitle(array("D5P"));
		$indexD1T = $this->csvLoader->getIndexTitle(array("D1T"));
		$indexD2T = $this->csvLoader->getIndexTitle(array("D2T"));
		$indexD3T = $this->csvLoader->getIndexTitle(array("D3T"));
		$indexD4T = $this->csvLoader->getIndexTitle(array("D4T"));
		$indexD5T = $this->csvLoader->getIndexTitle(array("D5T"));
		
		/* Si l'import est définitif, on regarde le résultat */
		if($definitif)
		{
			$indexResultat = $this->csvLoader->getIndexTitle(array("resultat"));
		}

		foreach ($data as $ligne) {
			$c = new Composante();
			/* Récupération du numéro de la composante */
			$c->codeComposante = $ligne[$indexCodComposante];
			try{
				$numComp = $this->composanteM->recupererNum($c);	
			}
			catch(Expression $e)
			{
				$_SESSION['erreurs'][] = $e->getMessage();
				continue;
			}

			$et = new Etudiant();
			/* On verifie si le numéro d'étudiant existe */
			try{
				$et = $this->etudiantM->recupererParNum($ligne[$indexNumEtudiant]);
			}
			catch(Expression $e)
			{
				$_SESSION['erreurs'][] = $e->getMessage();
				continue;
			}
			foreach ($this->epreuvesLib as $lib) {
				$v = new Validation();
				$v->numEnseignant = $_SESSION["numEnseignant"];
				$v->numEtudiant = $et->numEtudiant;

				$e = new Epreuve();
				$e->numComposante = $numComp;
				$e->libEpreuve = $lib;

				$v->idEpreuve = $this->epreuveM->recupererNum($e);
				
				/* On récupère le résultat de l'étudiant dans cette épreuve */
				$index = $this->csvLoader->getIndexTitle(array($lib));
				if(array_key_exists($index, $ligne))
				{
					$v->valeurValidation = $ligne[$index];
					try{
						$this->majValidation($v, $_SESSION['admin']);
					}
					catch(Exception $e)
					{
						$_SESSION['erreurs'][] = "Etudiant : ".$et->numEtudiant." - ".$e->getMessage();
					}

					$v->idValidation = null;
				}
				else
				{
					$_SESSION['erreurs'][] = "Résultat pour l'épreuve $lib de l'étudiant $et->numEtudiant manquant.";
					continue 2;
				}
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