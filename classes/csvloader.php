<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once($rootPath."/info606/outils_php/arrayTools.php");

class CSVLoader{

	private $filepath;
	private $file;
	private $titleLine;

	public function __construct($filepath,
								$titles, /* Tableau de mots qui composent la ligne de titres */
								$delimiter=",",
								$enclosure="\"",
								$escape = "\\")
	{
		if(!file_exists($filepath))
		{
			throw new Exception("Le fichier $filepath est introuvable");
		}
		$this->filepath = $filepath;
		$this->file = new SplFileObject($this->filepath);
		$this->file->setFlags(SplFileObject::READ_CSV | SplFileObject::DROP_NEW_LINE | SplFileObject::SKIP_EMPTY);
		$this->file->setCsvControl($delimiter, $enclosure, $escape);

		/* On recherche la ligne des titres */
		$this->titleLine = 1;
		$finded = false;
		if(count($titles) > 0)
		{
			/* Tant que la ligne n'a pas été trouvée et que la fin du fichier n'a pas été atteinte */
			while(!$finded && !$this->file->eof())
			{
				/* On récupère la ligne courante */
				$line = $this->file->current();
				/* On enlève les colonnes vides */
				$line = array_filter($line, "emptyFilter");
				/* On regarde si la ligne contient les titres */
				$finded = $this->isTitleLine($line, $titles);

				/* Si la ligne de titre n'a pas été trouvée, on passe à la ligne suivante */
				if(!$finded)
				{
					$this->titleLine++;
					$this->file->next();
				}
			}

			if(!$finded)
			{
				throw new Exception("Le fichier n'est pas valide car tous les titres nécessaires n'ont pas pu être trouvés.");
			}		}
	}

	private function isTitleLine($line, $array)
	{
		$contains = true;
		$indexWorld = 0;
		/* On parcours les mots à rechercher tant qu'un mot a toujours été trouvé */
		while($indexWorld < count($array) && $contains)
		{
			$contains = false;

			/* On parcours les colonnes du tableau CSV */
			foreach ($line as $value) 
			{
				/* Si le mot a été trouvé, on sort de la boucle et on recherche le mot suivant */
				if(stristr($value,$array[$indexWorld]))
				{
					$contains = true;
				}
			}

			/* On passe au mot suivant */
			$indexWorld++;
		}
		return $contains;
	}

	public function getHeadLine()
	{
		$this->file->seek($this->titleLine);
		$titles = $this->file->current();
		$titles = array_filter($titles, "emptyFilter");

		return $titles;
	}


	public function getIndexTitle($array)
	{
		$titles = $this->getHeadLine();
		$index = 0;
		$contains = false;

		/* On parcourt les colonnes de titres */
		while($index < count($titles) && !$contains)
		{
			$contains=true;
			$compteur = 0;

			if(array_key_exists($index, $titles))
			{
				/* Tant que le tableau n'est pas parcouru ou que l'ensemble des mots n'ont pas été trouvés */
				while($compteur < count($array) && $contains)
				{	
					/* Si le mot n'est pas trouvé, la colonne ne convient pas*/
					if(!stristr($titles[$index],$array[$compteur]))
					{
						$contains = false;
					}
					$compteur++;
				}

				/* Si la colonne ne contient pas les mots recherchés, on passe à la colonne suivante */
				if(!$contains)
					$index++;
			}
			else
				$index++;
		}

		return $contains?$index:-1;
	}

	public function getDataLine($lineNum)
	{
		$this->file->seek($lineNum);
		$data = $this->file->fgetcsv();

		return $data;
	}

	/* Il y a un bug provenant de PHP (https://bugs.php.net/bug.php?id=46569) avec le seek et fgetcsv
	*  Donc, utilisation de current() avec next().
	*/
	public function getData()
	{
		$data = array();

		/* On saute la ligne des titres */
		$this->file->seek($this->titleLine + 1);

		while($this->file->valid())
		{
			$ligne = $this->file->current();
			if($ligne)
			{
				/* On retire les cases vides */
				$ligne = array_filter($ligne, "emptyFilter");
				$data[] = $ligne;
			}
			$this->file->next();
		}

		/* On retire les lignes vides */
		$data = array_filter($data, "emptyArrayFilter");
		return $data;
	}
}