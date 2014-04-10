<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class RegimeManager
{
	private $_myPDO;

	public function __construct( $myPDO )
	{
		$this->_myPDO = $myPDO;
	}

	public function exists(Regime $r)
	{
		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Regime
			WHERE numRegime=:id
SQL
		);
		$q->bindValue(':id', 		$r->numRegime);
		$q->execute();
		$data = $q->fetch(PDO::FETCH_ASSOC);
		if ($data['nb'] != 0)
		{
			return true;
		}
		return false;
	}

	public function ajouter(Regime $r)
	{
		if ($this->exists($r))
			throw new Exception("Impossible d'ajouter le regime car le numéro ".$r->numRegime." est déjà utilisé.");

		$q = $this->_myPDO->prepare(<<<SQL
			INSERT INTO regime(libRegime)
			VALUES (:lib)
SQL
		);

		$q->bindValue(":lib", 		$r->libRegime);
		$q->execute();
	}

	public function supprimer(Regime $r)
	{
		if (!$this->exists($r))
			throw new Exception("Impossible de supprimer le régime car il n'existe pas.");

		$q = $this->_myPDO->prepare(<<<SQL
			DELETE FROM Regime
			WHERE numRegime=:num
SQL
		);

		$q->bindValue(':num', $r->numRegime);
		$q->execute();
	}

	public function recupererParNum($num)
	{
		$r = new Regime();

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Regime
			WHERE numRegime=:num
SQL
		);

		$q->bindValue(":num", $num);
		$q->execute();

		$data = $q->fetch(PDO::FETCH_ASSOC);
		if($data['nb'] == 0)
			throw new Exception("Il n'existe aucun regime avec le numéro $num.");
		elseif($data['nb'] > 1)
			throw new Exception("Il existe plusieurs regimes avec le numéro $num.");

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT *
			FROM Regime
			WHERE numRegime=:num
SQL
		);

		$q->bindValue(":num", $num);
		$q->execute();

		$res = $q->fetch(PDO::FETCH_ASSOC);

		$r->numRegime = $res['NUMREGIME'];
		$r->libRegime = $res['LIBREGIME'];

		return $r;
	}

	public function recupererTout()
	{
		$q = $this->_myPDO->query(<<<SQL
			SELECT *
			FROM Regime
			ORDER BY numRegime
SQL
		);
		$q->execute();
		$tabRegimes[] = array();
		while($res = $q->fetch(PDO::FETCH_ASSOC))
		{
			$r = new Regime();
			
			$r->numRegime = $res['NUMREGIME'];
			$r->libRegime = $res['LIBREGIME'];

			$tabRegimes[] = $r; 
		}

		return $tabRegimes;
	}

	public function maj(Regime $r)
	{

		if (!$this->exists($r))
			throw new Exception("Le régime n°".$r->numRegime." n'existe pas.");

		$q = $this->_myPDO->prepare(<<<SQL
			UPDATE Regime
			SET	libRegime=:lib
			WHERE numRegime=:num
SQL
		);

		$q->bindValue(":lib", 		$c->libRegime);
		$q->bindValue(":num", 		$c->numRegime);

		$q->execute();
	}


}