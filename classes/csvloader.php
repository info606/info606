<?php

function emptyFilter($var)
{
        return (!empty($var));
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

	public function getData()
	{
		$data = array();
		$i = 1;

		while (!$this->file->eof()) {
			$ligne = array();
			/*$temp = $this->file->fgetcsv();
			for($j = 0; $j < count($titles); $j++)
			{
				$ligne[$titles[$j]] = $temp[$j];
			}*/
			$temp = $this->file->fgetcsv();
			print_r($temp);
			$temp = array_filter($temp, "emptyFilter");
		    $data[] = $temp;
		    $i++;
		}

		return $data;
	}
}