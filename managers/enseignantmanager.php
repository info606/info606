<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class EnseignantManager
{
	private $_myPDO;

	public function __construct( $myPDO )
	{
		$this->_myPDO = $myPDO;
	}

	public function exists(Enseignant $e)
	{
		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Enseignant
			WHERE loginEnseignant=:id
SQL
		);
		$q->bindValue(':id', 		$e->loginEnseignant);
		$q->execute();
		$data = $q->fetch(PDO::FETCH_ASSOC);
		if ($data['nb'] != 0)
		{
			return true;
		}
		return false;
	}

	public function ajouter(Enseignant $e)
	{
		if ($this->exists($e))
			throw new Exception("Impossible d'ajouter l'enseignant car le numéro ".$e->numEnseignant." est déjà utilisé.");

		$q = $this->_myPDO->prepare(<<<SQL
			INSERT INTO enseignant(numenseignant, nomenseignant, prenomEnseignant, loginEnseignant, mdpenseignant, numComposante, admin)
			VALUES (:num, :nom, :prenom, :login, :mdp, :numComposante, :admin)
SQL
		);

		$q->bindValue(":num", 		$e->numEnseignant);
		$q->bindValue(":nom", 		$e->nomEnseignant);
		$q->bindValue(":prenom", 		$e->prenomEnseignant);
		$q->bindValue(":login", 		$e->loginEnseignant);
		$q->bindValue(":mdp", 		$e->mdpEnseignant);
		$q->bindValue(":numComposante", $e->numComposante);
		$q->bindValue(":admin", $e->admin);
		$q->execute();
	}

	public function supprimer(Enseignant $e)
	{
		if (!$this->exists($e))
			throw new Exception("Impossible de supprimer l'enseignant car il n'existe pas.");

		$q = $this->_myPDO->prepare(<<<SQL
			DELETE FROM enseignant
			WHERE loginEnseignant=:num
SQL
		);

		$q->bindValue(':num', $e->loginEnseignant);
		$q->execute();
	}

	public function recupererParNum($num)
	{
		$e = new Enseignant();

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Enseignant
			WHERE numEnseignant=:num
SQL
		);

		$q->bindValue(":num", $num);
		$q->execute();

		$data = $q->fetch(PDO::FETCH_ASSOC);
		if($data['nb'] == 0)
			throw new Exception("Il n'existe aucun enseignant avec le numéro $num.");
		elseif($data['nb'] > 1)
			throw new Exception("Il existe plusieurs enseignants avec le numéro $num.");

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT *
			FROM Enseignant
			WHERE numEnseignant=:num
SQL
		);

		$q->bindValue(":num", $num);
		$q->execute();

		$res = $q->fetch(PDO::FETCH_ASSOC);

		$e->numEnseignant = $res['NUMENSEIGNANT'];
		$e->nomEnseignant = $res['NOMENSEIGNANT'];
		$e->prenomEnseignant = $res['PRENOMENSEIGNANT'];
		$e->loginEnseignant = $res['LOGINENSEIGNANT'];
		$e->mdpEnseignant = $res['MDPENSEIGNANT'];
		$e->admin = $res['ADMIN'];
		$e->numComposante = $res['NUMCOMPOSANTE'];

		return $e;
	}

	public function recupererParLogin($num)
	{
		$e = new Enseignant();

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Enseignant
			WHERE loginEnseignant=:login
SQL
		);

		$q->bindValue(":login", $num);
		$q->execute();

		$data = $q->fetch(PDO::FETCH_ASSOC);
		if($data['nb'] == 0)
			throw new Exception("Il n'existe aucun enseignant avec le login $num.");
		elseif($data['nb'] > 1)
			throw new Exception("Il existe plusieurs enseignants avec le login $num.");

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT *
			FROM Enseignant
			WHERE loginEnseignant=:num
SQL
		);

		$q->bindValue(":num", $num);
		$q->execute();

		$res = $q->fetch(PDO::FETCH_ASSOC);

		$e->numEnseignant = $res['NUMENSEIGNANT'];
		$e->nomEnseignant = $res['NOMENSEIGNANT'];
		$e->prenomEnseignant = $res['PRENOMENSEIGNANT'];
		$e->loginEnseignant = $res['LOGINENSEIGNANT'];
		$e->mdpEnseignant = $res['MDPENSEIGNANT'];
		$e->numComposante = $res['NUMCOMPOSANTE'];
		$e->admin = $res['ADMIN'];

		return $e;
	}

	public function recupererTout()
	{
		$q = $this->_myPDO->query(<<<SQL
			SELECT *
			FROM Enseignant
			ORDER BY numEnseignant
SQL
		);
		$q->execute();
		$tabEnseignant[] = array();
		while($res = $q->fetch(PDO::FETCH_ASSOC))
		{
			$e = new Enseignant();

			$e->numEnseignant = $res['NUMENSEIGNANT'];
			$e->nomEnseignant = $res['NOMENSEIGNANT'];
			$e->prenomEnseignant = $res['PRENOMENSEIGNANT'];
			$e->loginEnseignant = $res['LOGINENSEIGNANT'];
			$e->mdpEnseignant = $res['MDPENSEIGNANT'];
			$e->numComposante = $res['NUMCOMPOSANTE'];
			$e->admin = $res['ADMIN'];

			$tabEnseignant[] = $e; 
		}

		return $tabEnseignant;
	}

	public function maj(Enseignant $e)
	{

		if (!$this->exists($e))
			throw new Exception("L'enseignant n° ".$e->numEnseignant." n'existe pas.");

		$q = $this->_myPDO->prepare(<<<SQL
			UPDATE Enseignant
			SET	nomEnseignant=:nom, 
				prenomEnseignant=:prenom,
				loginEnseignant=:login, 
				mdpEnseignant=:mdp,
				numComposante=:numComposante,
				admin=:admin
			WHERE numEnseignant=:num
SQL
		);

		$q->bindValue(":nom", 		$e->nomEnseignant);
		$q->bindValue(":prenom", 		$e->prenomEnseignant);
		$q->bindValue(":login", 		$e->loginEnseignant);
		$q->bindValue(":mdp", 		$e->mdpEnseignant);
		$q->bindValue(":num",		$e->numEnseignant);
		$q->bindValue(":numComposante", $e->numComposante);
		$q->bindValue(":admin", $e->admin);
		$q->execute();
	}


}