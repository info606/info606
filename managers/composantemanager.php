<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class ComposanteManager
{
	private $_myPDO;

	public function __construct( $myPDO )
	{
		$this->_myPDO = $myPDO;
	}

	public function exists(Composante $c)
	{
		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Composante
			WHERE codecomposante=:code AND libcomposante LIKE :lib
SQL
		);
		$q->bindValue(':code', 		$c->codeComposante);
		$q->bindValue(':lib', 		$c->libComposante);
		$q->execute();
		$data = $q->fetch(PDO::FETCH_ASSOC);
		if ($data['nb'] != 0)
		{
			return true;
		}
		return false;
	}

	public function ajouter(Composante $c)
	{
		if ($this->exists($c))
			return;

		$q = $this->_myPDO->prepare(<<<SQL
			INSERT INTO composante(codeComposante, libComposante)
			VALUES (:code, :lib)
SQL
		);

		$q->bindValue(":code", 		$c->codeComposante);
		$q->bindValue(":lib", 		$c->libComposante);
		$q->execute();
	}

	public function supprimer(Composante $c)
	{
		if (!$this->exists($c))
			throw new Exception("Impossible de supprimer la composante car elle n'existe pas.");
		else
			$num = $this->recupererNum($c);

		$q = $this->_myPDO->prepare(<<<SQL
			DELETE FROM composante
			WHERE numComposante=:num
SQL
		);

		$q->bindValue(':num', $num);
		$q->execute();
	}

	public function recupererNum(Composante $c)
	{
		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Composante
			WHERE codeComposante=:code OR libComposante LIKE :lib
SQL
		);

		$q->bindValue(':code', 		$c->codeComposante);
		$q->bindValue(':lib', 		$c->libComposante);
		$q->execute();

		$data = $q->fetch(PDO::FETCH_ASSOC);
		if($data['nb'] == 0)
			throw new Exception("Il n'existe aucune composante correspondante ($c->codeComposante | $c->libComposante).");

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT numComposante
			FROM Composante
			WHERE codeComposante=:code OR libComposante LIKE :lib
SQL
		);

		$q->bindValue(':code', 		$c->codeComposante);
		$q->bindValue(':lib', 		$c->libComposante);
		$q->execute();

		$res = $q->fetch(PDO::FETCH_ASSOC);

		return $res["numComposante"];
	}

	public function recupererParNum($num)
	{
		$c = new Composante();

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Composante
			WHERE numComposante=:num
SQL
		);

		$q->bindValue(":num", $num);
		$q->execute();

		$data = $q->fetch(PDO::FETCH_ASSOC);
		if($data['nb'] == 0)
			throw new Exception("Il n'existe aucune composante avec le numéro $num.");
		elseif($data['nb'] > 1)
			throw new Exception("Il existe plusieurs composantes avec le numéro $num.");

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT *
			FROM Composante
			WHERE numComposante=:num
SQL
		);

		$q->bindValue(":num", $num);
		$q->execute();

		$res = $q->fetch(PDO::FETCH_ASSOC);

		$c->numComposante = $res['NUMCOMPOSANTE'];
		$c->codeComposante = $res['CODECOMPOSANTE'];
		$c->libComposante = $res['LIBCOMPOSANTE'];

		return $c;
	}

	public function recupererTout()
	{
		$q = $this->_myPDO->query(<<<SQL
			SELECT *
			FROM Composante
			ORDER BY numComposante
SQL
		);
		$q->execute();
		$tabComposantes[] = array();
		while($res = $q->fetch(PDO::FETCH_ASSOC))
		{
			$c = new Composante();

			$c->numComposante = $res['NUMCOMPOSANTE'];
			$c->codeComposante = $res['CODECOMPOSANTE'];
			$c->libComposante = $res['LIBCOMPOSANTE'];

			$tabComposantes[] = $c; 
		}

		return $tabComposantes;
	}

	public function maj(Composante $c)
	{

		if (!$this->exists($c))
			throw new Exception("La composante n°".$c->numComposante." n'existe pas.");

		$q = $this->_myPDO->prepare(<<<SQL
			UPDATE Composante
			SET	codeComposante=:code, libComposante=:lib
			WHERE numComposante=:num
SQL
		);

		$q->bindValue(":lib", 		$c->libComposante);
		$q->bindValue(":code",		$c->codeComposante);
		$q->bindValue(":num", 		$c->numComposante);

		$q->execute();
	}


}