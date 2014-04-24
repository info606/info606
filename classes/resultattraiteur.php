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
	private $titres = array("Composante (code)","Code Etudiant","Etape (code)");
	
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
		catch(Expression $e)
		{
			$_SESSION['erreurs'][] = $e->getMessage();
			exit();
		}

		$c = new Composante();
		$v = new Validation();
		$et = new Etudiant();

		$data = $this->csvLoader->getData();
		foreach ($data as $ligne) {
			/* Récupération du numéro de la composante */
			$index = $this->csvLoader->getIndexTitle(array("composante", "code"));
			$c->libComposante = $ligne[$index];
			try{
				$numComp = $this->composanteM->recupererNum($c);	
			}
			catch(Expression $e)
			{
				$_SESSION['erreurs'][] = $e->getMessage();
				continue;
			}

			$index = $this->csvLoader->getIndexTitle(array("Code", "Etudiant"));
			/* On verifie si le numéro d'étudiant existe */
			try{
				$et = $this->etudiantM->recupererParNum($ligne[$index]);
			}
			catch(Expression $e)
			{
				$_SESSION['erreurs'][] = $e->getMessage();
				continue;
			}

			$v->numEnseignant = $_SESSION["numEnseignant"];
			$v->numEtudiant = $et->numEtudiant;
			
			/* Insertion des épreuves (si elles n'existent pas déjà) */
			foreach ($this->epreuvesLib as $lib) {
				$e = new Epreuve();
				$e->numComposante = $numComp;
				$e->libEpreuve = $lib;

				$v->idEpreuve = $this->epreuveM->recupererNum($e);
				
				/* On récupère le résultat de l'étudiant dans cette épreuve */
				$index = $this->csvLoader->getIndexTitle(array($lib));
				if(isset($ligne[$index]))
				{
					$v->valeurValidation = $ligne[$index];
					try{
						$this->majValidation($v, $_SESSION['admin']);
					}
					catch(Exception $e)
					{
						$_SESSION['erreurs'][] = $e->getMessage();
					}

					$v->idValidation = null;
				}
				else
				{
					$_SESSION['erreurs'][] = "Résultat pour l'épreuve $lib de l'étudiant $numEtudiant manquant.";
					continue 2;
				}
			}
		}
	}

	private function majValidation($validation, $admin)
	{
		if($this->validationM->exists($v))
		{
			$idValid = $this->validationM->recupererNum($v);
			/* On récupère l'ancienne valeur de la validation */
			$vRecup = $this->validationM->recupererParNum($idValid);

			/* Cas où la valeur rétrograderait */
			if($vRecup->valeurValidation > $validation->valeurValidation && !$admin)
			{
				throw new Exception("Vous n'avez pas les droits nécessaires pour rétrograder une validation");
			}
			else
			{
				$v->idValidation = $idValid;
				$this->validationM->maj($v);
			}
		}
		else
		{
			$this->validationM->ajouter($v);
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