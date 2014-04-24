<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class EpreuveManager
{
	private $_myPDO;

	public function __construct( $myPDO )
	{
		$this->_myPDO = $myPDO;
	}

	public function exists(Epreuve $e)
	{
		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Epreuve
			WHERE (libEpreuve LIKE :lib AND numComposante = :num) OR idEpreuve = :id
SQL
		);
		$q->bindValue(':id', 		$e->idEpreuve);
		$q->bindValue(':lib', 		$e->libEpreuve);
		$q->bindValue(':num', 		$e->numComposante);
		$q->execute();
		$data = $q->fetch(PDO::FETCH_ASSOC);
		if ($data['nb'] != 0)
		{
			return true;
		}
		return false;
	}

	public function ajouter(Epreuve $e)
	{
		if ($this->exists($e))
			return;

		$q = $this->_myPDO->prepare(<<<SQL
			INSERT INTO epreuve(numComposante, libEpreuve)
			VALUES (:numCom, :lib)
SQL
		);

		$q->bindValue(":numCom", 		$e->numComposante);
		$q->bindValue(":lib", 		$e->libEpreuve);
		$q->execute();
	}

	public function supprimer(Epreuve $e)
	{
		if (!$this->exists($e))
			throw new Exception("Impossible de supprimer l'épreuve car elle n'existe pas.");

		$q = $this->_myPDO->prepare(<<<SQL
			DELETE FROM Epreuve
			WHERE idEpreuve=:id
SQL
		);

		$q->bindValue(':id', $e->idEpreuve);
		$q->execute();
	}

	public function recupererNum($e)
	{
		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Epreuve
			WHERE libEpreuve LIKE :lib AND numComposante = :num
SQL
		);
		$q->bindValue(':num', 		$e->numComposante);
		$q->bindValue(':lib', 		$e->libEpreuve);
		$q->execute();

		$data = $q->fetch(PDO::FETCH_ASSOC);
		if($data['nb'] == 0)
			throw new Exception("Il n'existe aucune épreuve avec le numéro $num.");
		elseif($data['nb'] > 1)
			throw new Exception("Il existe plusieurs épreuves avec le numéro $num.");

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT idEpreuve as 'id'
			FROM Epreuve
			WHERE libEpreuve LIKE :lib AND numComposante = :num
SQL
		);
		$q->bindValue(':num', 		$e->numComposante);
		$q->bindValue(':lib', 		$e->libEpreuve);
		$q->execute();

		$res = $q->fetch(PDO::FETCH_ASSOC);

		return $res["id"];
	}

	public function recupererParNum($num)
	{
		$e = new Epreuve();

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Epreuve
			WHERE idEpreuve=:id
SQL
		);

		$q->bindValue(":id", $num);
		$q->execute();

		$data = $q->fetch(PDO::FETCH_ASSOC);
		if($data['nb'] == 0)
			throw new Exception("Il n'existe aucune épreuve avec le numéro $num.");
		elseif($data['nb'] > 1)
			throw new Exception("Il existe plusieurs épreuves avec le numéro $num.");

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT *
			FROM Epreuve
			WHERE idEpreuve=:id
SQL
		);

		$q->bindValue(":id", $num);
		$q->execute();

		$res = $q->fetch(PDO::FETCH_ASSOC);

		$e->idEpreuve = $res['IDEPREUVE'];
		$e->numComposante = $res['NUMCOMPOSANTE'];
		$e->libEpreuve = $res['LIBEPREUVE'];

		return $e;
	}

	public function recupererParNumComposante($num)
	{
		$e = new Epreuve();

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Epreuve
			WHERE numComposante=:id
SQL
		);

		$q->bindValue(":id", $num);
		$q->execute();

		$data = $q->fetch(PDO::FETCH_ASSOC);
		if($data['nb'] == 0)
			throw new Exception("Il n'existe aucune épreuve avec le numéro de composante $num.");

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT *
			FROM Epreuve
			WHERE numComposante=:id
SQL
		);

		$q->bindValue(":id", $num);
		$q->execute();

		$tabEpreuves[] = array();
		while($res = $q->fetch(PDO::FETCH_ASSOC))
		{
			$e = new Epreuve();
			
			$e->idEpreuve = $res['IDEPREUVE'];
			$e->numComposante = $res['NUMCOMPOSANTE'];
			$e->libEpreuve = $res['LIBEPREUVE'];

			$tabEpreuves[] = $e; 
		}

		return $tabEpreuves;
	}

	public function recupererTout()
	{
		$q = $this->_myPDO->query(<<<SQL
			SELECT *
			FROM Epreuve
			ORDER BY idEpreuve
SQL
		);
		$q->execute();
		$tabEpreuves[] = array();
		while($res = $q->fetch(PDO::FETCH_ASSOC))
		{
			$e = new Epreuve();
			
			$e->idEpreuve = $res['IDEPREUVE'];
			$e->numComposante = $res['NUMCOMPOSANTE'];
			$e->libEpreuve = $res['LIBEPREUVE'];

			$tabEpreuves[] = $e; 
		}

		return $tabEpreuves;
	}

	public function maj(Epreuve $e)
	{

		if (!$this->exists($e))
			throw new Exception("L'épreuve n°".$e->idEpreuve." n'existe pas.");

		$q = $this->_myPDO->prepare(<<<SQL
			UPDATE Epreuve
			SET	numComposante=:numCom, libEpreuve=:lib
			WHERE idEpreuve=:id
SQL
		);

		$q->bindValue(":id", 		$e->idEpreuve);
		$q->bindValue(":numCom", 		$e->numComposante);
		$q->bindValue(":lib", 		$e->libEpreuve);

		$q->execute();
	}


}