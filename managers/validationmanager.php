<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class ValidationManager
{
	private $_myPDO;

	public function __construct( $myPDO )
	{
		$this->_myPDO = $myPDO;
	}

	public function exists(Validation $v)
	{
		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Validation
			WHERE idValidation=:id OR (numEtudiant=:numEt AND idEpreuve=:idEp)
SQL
		);

		$q->bindValue(":numEt", $v->numEtudiant);
		$q->bindValue(":idEp", $v->idEpreuve);
		$q->bindValue(':id', 		$v->idValidation);
		$q->execute();
		$data = $q->fetch(PDO::FETCH_ASSOC);
		if ($data['nb'] != 0)
		{
			return true;
		}
		return false;
	}

	public function ajouter(Validation $v)
	{
		if ($this->exists($v))
			throw new Exception("Impossible d'ajouter la validation car le numéro ".$v->idValidation." est déjà utilisé.");

		$q = $this->_myPDO->prepare(<<<SQL
			INSERT INTO validation(numEnseignant, numEtudiant, idEpreuve, dateValidation)
			VALUES (:numEns, :numEt, :idEp, NOW())
SQL
		);

		$q->bindValue(":numEns", 		$v->numEnseignant);
		$q->bindValue(":numEt", 		$v->numEtudiant);
		$q->bindValue(":idEp", 		$v->idEpreuve);

		$q->execute();
	}

	public function supprimer(Validation $v)
	{
		if (!$this->exists($v))
			throw new Exception("Impossible de supprimer la validation car elle n'existe pas.");

		$q = $this->_myPDO->prepare(<<<SQL
			DELETE FROM Validation
			WHERE idValidation=:num
SQL
		);

		$q->bindValue(':num', $v->idValidation);
		$q->execute();
	}

	public function recupererNum($v)
	{
		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Validation
			WHERE numEtudiant=:numEt AND idEpreuve=:idEp
SQL
		);

		$q->bindValue(":numEt", $v->numEtudiant);
		$q->bindValue(":idEp", $v->idEpreuve);
		$q->execute();

		$data = $q->fetch(PDO::FETCH_ASSOC);
		if($data['nb'] == 0)
			throw new Exception("Il n'existe aucune validation avec l'&eacutetudiant $v->numEtudiant et l'&eacutepreuve $v->idEpreuve.");
		elseif($data['nb'] > 1)
			throw new Exception("Il existe plusieurs validations avec l'&eacutetudiant $v->numEtudiant et l'&eacutepreuve $v->idEpreuve.");

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT idValidation
			FROM Validation
			WHERE numEtudiant=:numEt AND idEpreuve=:idEp
SQL
		);

		$q->bindValue(":numEt", $v->numEtudiant);
		$q->bindValue(":idEp", $v->idEpreuve);
		$q->execute();

		$res = $q->fetch(PDO::FETCH_ASSOC);

		return $res["idValidation"];
	}

	public function recupererParNum($num)
	{
		$v = new Validation();

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Validation
			WHERE idValidation=:id
SQL
		);

		$q->bindValue(":id", $num);
		$q->execute();

		$data = $q->fetch(PDO::FETCH_ASSOC);
		if($data['nb'] == 0)
			throw new Exception("Il n'existe aucune validation avec le numéro $num.");
		elseif($data['nb'] > 1)
			throw new Exception("Il existe plusieurs validations avec le numéro $num.");

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT *
			FROM Validation
			WHERE idValidation=:id
SQL
		);

		$q->bindValue(":id", $num);
		$q->execute();

		$res = $q->fetch(PDO::FETCH_ASSOC);

		$v->idValidation = $res['IDVALIDATION'];
		$v->numEnseignant = $res['NUMENSEIGNANT'];
		$v->numEtudiant = $res['NUMETUDIANT'];
		$v->idEpreuve = $res['IDEPREUVE'];
		$v->dateValidation = $res['DATEVALIDATION'];

		return $v;
	}

	public function recupererParNumEtudiant($num)
	{
		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Validation
			WHERE numEtudiant=:id
SQL
		);

		$q->bindValue(":id", $num);
		$q->execute();

		$data = $q->fetch(PDO::FETCH_ASSOC);
		//if($data['nb'] == 0)
		//	throw new Exception("Il n'existe aucune validation avec le numéro $num.");

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT *
			FROM Validation
			WHERE numEtudiant=:id
SQL
		);

		$q->bindValue(":id", $num);
		$q->execute();

		$tab[] = array();

		while($res = $q->fetch(PDO::FETCH_ASSOC)){
			$v = new Validation();

			$v->idValidation = $res['IDVALIDATION'];
			$v->numEnseignant = $res['NUMENSEIGNANT'];
			$v->numEtudiant = $res['NUMETUDIANT'];
			$v->idEpreuve = $res['IDEPREUVE'];
			$v->dateValidation = $res['DATEVALIDATION'];

			$tab[] = $v;
		}
		

		return $tab;
	}

	public function recupererTout()
	{
		$q = $this->_myPDO->query(<<<SQL
			SELECT *
			FROM Validation
			ORDER BY idValidation
SQL
		);
		$q->execute();
		$tabValidations[] = array();
		while($res = $q->fetch(PDO::FETCH_ASSOC))
		{
			$v = new Validation();
			
			$v->idValidation = $res['IDVALIDATION'];
			$v->numEnseignant = $res['NUMENSEIGNANT'];
			$v->numEtudiant = $res['NUMETUDIANT'];
			$v->idEpreuve = $res['IDEPREUVE'];
			$v->dateValidation = $res['DATEVALIDATION'];

			$tabValidations[] = $v; 
		}

		return $tabValidations;
	}

	public function maj(Validation $v)
	{

		if (!$this->exists($v))
			throw new Exception("La validation n°".$v->idValidation." n'existe pas.");

		$q = $this->_myPDO->prepare(<<<SQL
			UPDATE Validation
			SET	numEnseignant=:numEns, numEtudiant=:numEt, idEpreuve=:idEp, dateValidation=(NOW())
			WHERE idValidation=:idVal
SQL
		);

		$q->bindValue(":idVal", 	$v->idValidation);
		$q->bindValue(":numEns", 	$v->numEnseignant);
		$q->bindValue(":numEt", 	$v->numEtudiant);
		$q->bindValue(":idEp", 		$v->idEpreuve);

		$q->execute();
	}


}