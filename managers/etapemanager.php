<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class EtapeManager
{
	private $_myPDO;

	public function __construct( $myPDO )
	{
		$this->_myPDO = $myPDO;
	}

	public function exists(Etape $e)
	{
		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Etape
			WHERE (codeEtape=:code AND versionEtape =:version AND numComposante=:numComposante AND idCursus=:cursus)
				OR idEtape=:idEtape
SQL
		);
		$q->bindValue(':code', 		$e->codeEtape);
		$q->bindValue(':version', 		$e->versionEtape);
		$q->bindValue(':numComposante', 		$e->numComposante);
		$q->bindValue(':idEtape', 		$e->idEtape);
		$q->bindValue(':cursus', 		$e->idCursus);
		$q->execute();
		$data = $q->fetch(PDO::FETCH_ASSOC);
		if ($data['nb'] != 0)
		{
			return true;
		}
		return false;
	}

	public function ajouter(Etape $e)
	{
		if ($this->exists($e))
		{
			var_dump($e);
			return;
		}

		$q = $this->_myPDO->prepare(<<<SQL
			INSERT INTO etape(codeEtape, idCursus, numComposante, libCourtEtape, versionEtape, libLongEtape)
			VALUES (:codeEt, :idCurs, :numComp, :libCourt, :versionEtape, :libLong)
SQL
		);

		$q->bindValue(":codeEt", 		$e->codeEtape);
		$q->bindValue(":idCurs", 		$e->idCursus);
		$q->bindValue(":numComp", 		$e->numComposante);
		$q->bindValue(":libCourt", 		$e->libCourtEtape);
		$q->bindValue(":versionEtape", 	$e->versionEtape);
		$q->bindValue(":libLong", 		$e->libLongEtape);
		$q->execute();
	}

	public function supprimer(Etape $e)
	{
		$num = $this->recupererNum($e);

		$q = $this->_myPDO->prepare(<<<SQL
			DELETE FROM etape
			WHERE idEtape=:id
SQL
		);

		$q->bindValue(':id', $num);
		$q->execute();
	}

	public function recupererNum(Etape $e)
	{
		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Etape
			WHERE codeEtape=:code AND versionEtape =:version AND numComposante=:numComposante AND idCursus=:cursus
SQL
		);
		$q->bindValue(':cursus', 		$e->idCursus);
		$q->bindValue(':code', 		$e->codeEtape);
		$q->bindValue(':numComposante', 		$e->numComposante, PDO::PARAM_INT);
		$q->bindValue(':version',	$e->versionEtape);

		
		$q->execute(); 
		$data = $q->fetch(PDO::FETCH_ASSOC);
		if($data['nb'] == 0)
			throw new Exception("Il n'existe aucune étape correspondante.");

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT idEtape
			FROM etape
			WHERE codeEtape=:code AND versionEtape =:version AND numComposante=:numComposante AND idCursus=:cursus
SQL
		);
		$q->bindValue(':cursus', 		$e->idCursus);
		$q->bindValue(':code', 		$e->codeEtape);
		$q->bindValue(':numComposante', 		$e->numComposante);
		$q->bindValue(':version',	$e->versionEtape);
		$q->execute();

		/* Si il y a plusieurs résultat on récupère le résultat avec la version la plus récente, soit le premier résultat */
		$res = $q->fetch(PDO::FETCH_ASSOC);

		return $res['idEtape'];
	}

	public function recupererParNum($num)
	{
		$e = new Etape();

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Etape
			WHERE idEtape=:id
SQL
		);

		$q->bindValue(":id", $num);
		$q->execute();

		$data = $q->fetch(PDO::FETCH_ASSOC);
		if($data['nb'] == 0)
			throw new Exception("Il n'existe aucune étape avec le numéro $num.");
		elseif($data['nb'] > 1)
			throw new Exception("Il existe plusieurs étapes avec le numéro $num.");

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT *
			FROM etape
			WHERE idEtape=:id
SQL
		);

		$q->bindValue(":id", $num);
		$q->execute();

		$res = $q->fetch(PDO::FETCH_ASSOC);

		$e->idEtape = $res['IDETAPE'];
		$e->codeEtape = $res['CODEETAPE'];
		$e->idCursus = $res['IDCURSUS'];
		$e->numComposante = $res['NUMCOMPOSANTE'];
		$e->libCourtEtape = $res['LIBCOURTETAPE'];
		$e->versionEtape = $res['VERSIONETAPE'];
		$e->libLongEtape = $res['LIBLONGETAPE'];
		

		return $e;
	}

	public function recupererTout()
	{
		$q = $this->_myPDO->query(<<<SQL
			SELECT *
			FROM Etape
			ORDER BY idEtape
SQL
		);
		$q->execute();
		$tabEtapes[] = array();
		while($res = $q->fetch(PDO::FETCH_ASSOC))
		{
			$e = new Etape();

			$e->idEtape = $res['IDETAPE'];
			$e->idCursus = $res['IDCURSUS'];
			$e->numComposante = $res['NUMCOMPOSANTE'];
			$e->libCourtEtape = $res['LIBCOURTETAPE'];
			$e->versionEtape = $res['VERSIONETAPE'];
			$e->libLongEtape = $res['LIBLONGETAPE'];

			$tabEtapes[] = $e; 
		}

		return $tabEtapes;
	}

	public function maj(Etape $e)
	{

		$num = $this->recupererNum($e);

		$q = $this->_myPDO->prepare(<<<SQL
			UPDATE ETAPE
			SET	codeEtape=:codeEt, idCursus=:idCursus, numComposante=:numComp, libCourtEtape=:libCourt, versionEtape=:versionEtape, libLongEtape=:libLong
			WHERE idEtape=:idEt
SQL
		);

		$q->bindValue(":idEt", 			$e->idEtape);
		$q->bindValue(":codeEt", 		$e->codeEtape);
		$q->bindValue(":idCurs", 		$e->idCursus);
		$q->bindValue(":numComp", 		$e->numComposante);
		$q->bindValue(":libCourt", 		$e->libCourtEtape);
		$q->bindValue(":versionEtape", 	$e->versionEtape);
		$q->bindValue(":libLong", 		$e->libLongEtape);

		$q->execute();
	}


}