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

	public function ajout($filename)
	{
		$this->csvLoader = new CSVLoader($this->path.$filename, ";");
		$c = new Composante();
		$v = new Validation();
		$et = new Etudiant();

		$data = $this->csvLoader->getData();
		foreach ($data as $ligne) {
			/* Récupération du numéro de la composante */
			$index = $this->csvLoader->getIndexTitle(array("composante"));
			$c->libComposante = $ligne[$index];
			try{
				$numComp = $this->composanteM->recupererNum($c);

				$index = $this->csvLoader->getIndexTitle(array("Num"));
				/* On verrifie si le numéro d'étudiant est valide */
				$et = $this->etudiantM->recupererParNum($ligne[$index]);

				$v->numEnseignant = $_SESSION["numEnseignant"];
				$v->numEtudiant = $et->numEtudiant;
				
				/* Insertion des épreuves (si elles n'existent pas déjà) */
				foreach ($this->epreuvesLib as $lib) {
					$e = new Epreuve();
					$e->numComposante = $numComp;
					$e->libEpreuve = $lib;

					if(!$this->epreuveM->exists($e))
					{
						$this->epreuveM->ajouter($e);	
					}
					else
					{
						$e->idEpreuve = $this->epreuveM->recupererNum($e);
						$this->epreuveM->maj($e);
					}

					$v->idEpreuve = $this->epreuveM->recupererNum($e);
					
					/* On récupère le résultat de l'étudiant dans cette épreuve */
					$index = $this->csvLoader->getIndexTitle(array($lib));
					if(isset($ligne[$index]) && $ligne[$index] == 1)
					{
						if(!$this->validationM->exists($v))
						{
							$this->validationM->ajouter($v);
						}
						else
						{
							$v->idValidation = $this->validationM->recupererNum($v);
							$this->validationM->maj($v);
						}
						$v->idValidation = null;
					}
					else
					{
						if($this->validationM->exists($v))
						{
							$v->idValidation = $this->validationM->recupererNum($v);
							$this->validationM->supprimer($v);
						}
					}
					
				}
			}
			catch(Exception $e)
			{
				$_SESSION['erreurs'][] = $e->getMessage();
			}
		}
	}

	public function suppression($filename)
	{
		echo "Suppression";
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
			$this->ajout($value);
		}
	}

}