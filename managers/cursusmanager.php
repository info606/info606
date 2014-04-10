<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class CursusManager
{
	private $_myPDO;

	public function __construct( $myPDO )
	{
		$this->_myPDO = $myPDO;
	}

	public function exists(Cursus $c)
	{
		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Cursus
			WHERE codeCursus=:code AND libCursus LIKE :lib AND niveau=:niveau
SQL
		);
		$q->bindValue(':code', 		$c->codeCursus);
		$q->bindValue(':lib', 		$c->libCursus);
		$q->bindValue(':niveau', 	$c->niveau);
		$q->execute();
		$data = $q->fetch(PDO::FETCH_ASSOC);
		if ($data['nb'] != 0)
		{
			return true;
		}
		return false;
	}

	public function ajouter(Cursus $c)
	{
		if ($this->exists($c))
			return;

		$q = $this->_myPDO->prepare(<<<SQL
			INSERT INTO cursus(codeCursus, libCursus, niveau)
			VALUES (:code, :lib, :niveau)
SQL
		);

		$q->bindValue(':code', 		$c->codeCursus);
		$q->bindValue(":lib", 		$c->libCursus);
		$q->bindValue(":niveau", 		$c->niveau);
		$q->execute();
	}

	public function supprimer(Cursus $c)
	{
		$num = $this->recupererNum($c);

		$q = $this->_myPDO->prepare(<<<SQL
			DELETE FROM cursus
			WHERE codeCursus=:num
SQL
		);

		$q->bindValue(':num', $num);
		$q->execute();
	}

	public function recupererNum(Cursus $c)
	{
		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Cursus
			WHERE codeCursus=:code AND libCursus LIKE :lib AND niveau=:niveau
SQL
		);
		$q->bindValue(':code', 		$c->codeCursus);
		$q->bindValue(':lib', 		$c->libCursus);
		$q->bindValue(':niveau', 	$c->niveau);
		$q->execute();

		$data = $q->fetch(PDO::FETCH_ASSOC);
		if($data['nb'] == 0)
			throw new Exception("Il n'existe aucun cursus correspondant.");
		
		$q = $this->_myPDO->prepare(<<<SQL
			SELECT idCursus
			FROM cursus
			WHERE codeCursus=:code AND libCursus LIKE :lib AND niveau=:niveau
SQL
		);
		$q->bindValue(':code', 		$c->codeCursus);
		$q->bindValue(':lib', 		$c->libCursus);
		$q->bindValue(':niveau', 	$c->niveau);
		$q->execute();

		$res = $q->fetch(PDO::FETCH_ASSOC);
		return $res['idCursus'];
	}

	public function recupererParNum($num)
	{
		$c = new Cursus();

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Cursus
			WHERE codeCursus=:num
SQL
		);

		$q->bindValue(":num", $num);
		$q->execute();

		$data = $q->fetch(PDO::FETCH_ASSOC);
		if($data['nb'] == 0)
			throw new Exception("Il n'existe aucun cursus avec le numéro $num.");
		elseif($data['nb'] > 1)
			throw new Exception("Il existe plusieurs cursus avec le numéro $num.");

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT *
			FROM Cursus
			WHERE codeCursus=:num
SQL
		);

		$q->bindValue(":num", $num);
		$q->execute();

		$res = $q->fetch(PDO::FETCH_ASSOC);

		$c->idCursus = $res['IDCURSUS'];
		$c->codeCursus = $res['CODECURSUS'];
		$c->libCursus = $res['LIBCURSUS'];
		$c->niveau = $res['NIVEAU'];

		return $c;
	}

	public function recupererTout()
	{
		$q = $this->_myPDO->query(<<<SQL
			SELECT *
			FROM Cursus
			ORDER BY codeCursus
SQL
		);
		$q->execute();
		$tabCursus[] = array();
		while($res = $q->fetch(PDO::FETCH_ASSOC))
		{
			$c = new Cursus();

			$c->idCursus = $res['IDCURSUS'];
			$c->codeCursus = $res['CODECURSUS'];
			$c->libCursus = $res['LIBCURSUS'];
			$c->niveau = $res['NIVEAU'];

			$tabCursus[] = $c; 
		}

		return $tabCursus;
	}

	public function maj(Cursus $c)
	{

		$num = $this->recupererNum($c);

		$q = $this->_myPDO->prepare(<<<SQL
			UPDATE Cursus
			SET	codeCursus=:code, libCursus LIKE :lib, niveau=:niveau
			WHERE idCursus=:num
SQL
		);

		$q->bindValue(":lib", 		$c->libCursus);
		$q->bindValue(":niveau", 		$c->niveau);
		$q->bindValue(":code", 		$c->codeCursus);

		$q->execute();
	}


}