<?php

function emptyFilter($var)
{
    return (!empty($var));
}

function emptyArrayFilter($var)
{
    return (count($var) != 0);
}

class CSVLoader{

	private $filepath;
	private $file;

	public function __construct($filepath,
								$delimiter=",",
								$enclosure="\"",
								$escape = "\\")
	{
		if(!file_exists($filepath))
		{
			exit("Le fichier $filepath est introuvable");
		}

		$this->filepath = $filepath;
		$this->file = new SplFileObject($this->filepath);
		$this->file->setFlags(SplFileObject::READ_CSV | SplFileObject::DROP_NEW_LINE | SplFileObject::SKIP_EMPTY);
		$this->file->setCsvControl($delimiter, $enclosure, $escape);
	}

	public function getHeadLine()
	{
		$this->file->seek(0);
		$titles = $this->file->fgetcsv();
		$titles = array_filter($titles, "emptyFilter");

		return $titles;
	}


	public function getIndexTitle($array)
	{
		$titles = $this->getHeadLine();
		$index = 0;
		$contains = false;
		while($index < count($titles) && !$contains)
		{
			$contains=true;
			$compteur = 0;
			while($compteur < count($array) && $contains)
			{	
				if(!stristr($titles[$index],$array[$compteur]))
				{
					$contains = false;
				}
				$compteur++;
			}

			if(!$contains)
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
		$this->file->seek(1);

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