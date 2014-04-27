<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once($rootPath."/info606/outils_php/autoload.php");
require_once($rootPath."/info606/outils_php/arrayTools.php");
require_once($rootPath."/info606/outils_php/dateTools.php");

class EtapeTraiteur extends Traiteur
{
	private $composanteM;
	private $etapeM;
	private $cursusM;
	private $titres = array("Composante (code)","Etape (code)","Etape (lib.)", "Version d'étape (code)", "lib. web","Cursus LMD (code)", "Cursus LMD (lib.)", "Niveau dans le diplôme");
	
	public function __construct()
	{
		$this->csvLoader = null;
		$this->path = $_SERVER['DOCUMENT_ROOT']."/info606/donnees/etapes/";

		$composanteC = new ComposanteControleur();
		$etapeC = new EtapeControleur();
		$cursusC = new CursusControleur();

		$this->composanteM = $composanteC->composanteManager;
		$this->etapeM = $etapeC->etapeManager;
		$this->cursusM = $cursusC->cursusManager;
	}

	public function traiter($filename, $definitif=false)
	{
		try{
			$this->csvLoader = new CSVLoader($this->path.$filename, $this->titres, ";");
		}
		catch(Exception $e)
		{
			$_SESSION['erreurs'][] = $e->getMessage();
			return;
		}

		$indexCodComp = $this->csvLoader->getIndexTitle(array("Composante","code"));
		$indexLibComp = $this->csvLoader->getIndexTitle(array("Composante","lib"));
		
		$indexCodCursus = $this->csvLoader->getIndexTitle(array("cursus","code"));
		$indexLibCursus = $this->csvLoader->getIndexTitle(array("cursus","lib"));
		$indexNiveau = $this->csvLoader->getIndexTitle(array("niveau"));

		$indexCodEtape = $this->csvLoader->getIndexTitle(array("etape","code"));
		$indexLibCEtape = $this->csvLoader->getIndexTitle(array("etape","lib"));
		$indexVersEtape = $this->csvLoader->getIndexTitle(array("Version","code"));
		$indexLibLEtape = $this->csvLoader->getIndexTitle(array("Version","lib", "web"));

		$data = $this->csvLoader->getData();
		$importErr = "Impossible d'importer la ligne : ";
		foreach ($data as $ligne) {

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
			if(!array_key_exists($indexCodCursus, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le code du cursus.";
				continue;
			}
			if(!array_key_exists($indexLibCursus, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le libellé du cursus.";
				continue;
			}
			if(!array_key_exists($indexNiveau, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le niveau dans le dîplome.";
				continue;
			}
			if(!array_key_exists($indexCodEtape, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le code de l'étape.";
				continue;
			}
			if(!array_key_exists($indexLibCEtape, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le libellé court de l'étape.";
				continue;
			}
			if(!array_key_exists($indexVersEtape, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque la version de l'étape.";
				continue;
			}
			if(!array_key_exists($indexLibLEtape, $ligne))
			{
				$_SESSION['erreurs'][] = $importErr."il manque le libellé long de l'étape.";
				continue;
			}

			$ligne = arrayToUTF8($ligne);
			$composante = new Composante();
			/* Récupération de la composante */
			$composante->codeComposante = $ligne[$indexCodComp];
			$composante->libComposante = $ligne[$indexLibComp];
			
			/* Insertion de la composante */
			if(!$this->composanteM->exists($composante))
			{
				$this->composanteM->ajouter($composante);	
			}
			$composante->numComposante = $this->composanteM->recupererNum($composante);

			$cursus = new Cursus();
			/* Récupération du cursus */
			$cursus->codeCursus = $ligne[$indexCodCursus];
			$cursus->libCursus = $ligne[$indexLibCursus];
			$cursus->niveau = $ligne[$indexNiveau];
	
			/* Insertion du cursus */
			if(!$this->cursusM->exists($cursus))
			{
				$this->cursusM->ajouter($cursus);
			}
			$cursus->idCursus = $this->cursusM->recupererNum($cursus);

			$etape = new Etape();
			/* Récupération de l'étape */
			$etape->codeEtape = $ligne[$indexCodEtape];
			$etape->libCourtEtape = $ligne[$indexLibCEtape];
			$etape->versionEtape = $ligne[$indexVersEtape];
			$etape->libLongEtape = $ligne[$indexLibLEtape];
			$etape->numComposante = $composante->numComposante;
			$etape->idCursus = $cursus->idCursus;
			var_dump($etape);

			/* Insertion de l'étape */
			if(! $this->etapeM->exists($etape))
			{
				$this->etapeM->ajouter($etape);
			}
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