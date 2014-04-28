<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class EtudiantManager
{
	private $_myPDO;

	public function __construct( $myPDO )
	{
		$this->_myPDO = $myPDO;
	}

	public function exists(Etudiant $e)
	{
		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Etudiant
			WHERE numEtudiant=:id OR loginEtudiant=:login
SQL
		);
		$q->bindValue(':id', 		$e->numEtudiant);
		$q->bindValue(':login',		$e->loginEtudiant);
		$q->execute();
		$data = $q->fetch(PDO::FETCH_ASSOC);
		if ($data['nb'] != 0)
		{
			return true;
		}
		return false;
	}

	public function existsParLogin(Etudiant $e)
	{
		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Etudiant
			WHERE loginEtudiant=:id
SQL
		);
		$q->bindValue(':id', 		$e->loginEtudiant);
		$q->execute();
		$data = $q->fetch(PDO::FETCH_ASSOC);
		if ($data['nb'] != 0)
		{
			return true;
		}
		return false;
	}

	public function ajouter(Etudiant $e)
	{
		// Si l'un etudiant a déjà le même id ou le même login on n'accepte pas
		if ($this->exists($e))
			return;

		$q = $this->_myPDO->prepare(<<<SQL
			INSERT INTO etudiant(numEtudiant, numRegime, idEtape, nomEtudiant, prenomEtudiant, mailEtudiant, dateNaisEtudiant, loginEtudiant, mdpEtudiant, dateIAEEtudiant, dateIAC2IEtudiant, C2IValide)
			VALUES (:num, :numR, :id, :nom, :prenom, :mail, :dateNais, :login, :mdp, :dateIAE, :dateIAC2I, :C2IValide)
SQL
		);

		$q->bindValue(":num", 		$e->numEtudiant);
		$q->bindValue(":numR", 			$e->numRegime);
		$q->bindValue(":id",	 		$e->idEtape);
		$q->bindValue(":nom", 		$e->nomEtudiant);
		$q->bindValue(":prenom", 		$e->prenomEtudiant);
		$q->bindValue(":mail", 		$e->mailEtudiant);
		$q->bindValue(":dateNais", 		$e->dateNaisEtudiant);
		$q->bindValue(":login", 		$e->loginEtudiant);
		$q->bindValue(":mdp", 		$e->mdpEtudiant);
		$q->bindValue(":dateIAE", 		$e->dateIAEEtudiant);
		$q->bindValue(":dateIAC2I", 		$e->dateIAC2IEtudiant);
		$q->bindValue(":C2IValide", 	$e->C2IValide);
		$q->execute();
	}

	public function supprimer(Etudiant $e)
	{
		// Si l'etudiant n'existe pas, on ne peut pas supprimer
		if (!$this->exists($e))
			throw new Exception("Impossible de supprimer l'etudiant car il n'existe pas.");

		$q = $this->_myPDO->prepare(<<<SQL
			DELETE FROM etudiant
			WHERE numEtudiant=:num
SQL
		);

		$q->bindValue(':num', $e->numEtudiant);
		$q->execute();
	}

	public function recupererParNum($num)
	{
		$e = new Etudiant();

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Etudiant
			WHERE numEtudiant=:num
SQL
		);

		$q->bindValue(":num", $num);
		$q->execute();

		$data = $q->fetch(PDO::FETCH_ASSOC);
		if($data['nb'] == 0)
			throw new Exception("Il n'existe aucun Etudiant avec le numéro $num.");
		elseif($data['nb'] > 1)
			throw new Exception("Il existe plusieurs Etudiants avec le numéro $num.");

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT *
			FROM Etudiant
			WHERE numEtudiant=:num
SQL
		);

		$q->bindValue(":num", $num);
		$q->execute();

		$res = $q->fetch(PDO::FETCH_ASSOC);

		$e->numEtudiant = $res['NUMETUDIANT'];
		$e->nomEtudiant = $res['NOMETUDIANT'];
		$e->prenomEtudiant = $res['PRENOMETUDIANT'];
		$e->mailEtudiant = $res['MAILETUDIANT'];
		$e->dateNaisEtudiant = $res['DATENAISETUDIANT'];
		$e->loginEtudiant = $res['LOGINETUDIANT'];
		$e->mdpEtudiant = $res['MDPETUDIANT'];
		$e->dateIAEEtudiant = $res['DATEIAEETUDIANT'];
		$e->dateIAC2IEtudiant = $res['DATEIAC2IETUDIANT'];
		$e->numRegime = $res['NUMREGIME'];
		$e->idEtape = $res['IDETAPE'];
		$e->C2IValide = $res['C2IVALIDE'];

		return $e;
	}

	public function recupererParLogin($num)
	{
		$e = new Etudiant();

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT count(*) AS "nb"
			FROM Etudiant
			WHERE loginEtudiant=:num
SQL
		);

		$q->bindValue(":num", $num);
		$q->execute();

		$data = $q->fetch(PDO::FETCH_ASSOC);
		if($data['nb'] == 0)
			throw new Exception("Il n'existe aucun Etudiant avec le login $num.");
		elseif($data['nb'] > 1)
			throw new Exception("Il existe plusieurs Etudiants avec le login $num.");

		$q = $this->_myPDO->prepare(<<<SQL
			SELECT *
			FROM Etudiant
			WHERE loginEtudiant=:num
SQL
		);

		$q->bindValue(":num", $num);
		$q->execute();

		$res = $q->fetch(PDO::FETCH_ASSOC);

		$e->numEtudiant = $res['NUMETUDIANT'];
		$e->nomEtudiant = $res['NOMETUDIANT'];
		$e->prenomEtudiant = $res['PRENOMETUDIANT'];
		$e->mailEtudiant = $res['MAILETUDIANT'];
		$e->dateNaisEtudiant = $res['DATENAISETUDIANT'];
		$e->loginEtudiant = $res['LOGINETUDIANT'];
		$e->mdpEtudiant = $res['MDPETUDIANT'];
		$e->dateIAEEtudiant = $res['DATEIAEETUDIANT'];
		$e->dateIAC2IEtudiant = $res['DATEIAC2IETUDIANT'];
		$e->numRegime = $res['NUMREGIME'];
		$e->idEtape = $res['IDETAPE'];
		$e->C2IValide = $res['C2IVALIDE'];

		return $e;
	}

	public function recupererTout()
	{
		$q = $this->_myPDO->query(<<<SQL
			SELECT *
			FROM Etudiant
			ORDER BY numEtudiant
SQL
		);
		$q->execute();
		$tabEtudiant[] = array();
		while($res = $q->fetch(PDO::FETCH_ASSOC))
		{
			$e = new Etudiant();

			$e->numEtudiant = $res['NUMETUDIANT'];
			$e->nomEtudiant = $res['NOMETUDIANT'];
			$e->prenomEtudiant = $res['PRENOMETUDIANT'];
			$e->mailEtudiant = $res['MAILETUDIANT'];
			$e->dateNaisEtudiant = $res['DATENAISETUDIANT'];
			$e->loginEtudiant = $res['LOGINETUDIANT'];
			$e->mdpEtudiant = $res['MDPETUDIANT'];
			$e->dateIAEEtudiant = $res['DATEIAEETUDIANT'];
			$e->dateIAC2IEtudiant = $res['DATEIAC2IETUDIANT'];
			$e->numRegime = $res['NUMREGIME'];
			$e->idEtape = $res['IDETAPE'];
			$e->C2IValide = $res['C2IVALIDE'];

			$tabEtudiant[] = $e; 
		}

		return $tabEtudiant;
	}

	public function maj(Etudiant $e)
	{

		if (!$this->exists($e))
			throw new Exception("L'étudiant n° ".$e->numEtudiant." n'existe pas.");

		$q = $this->_myPDO->prepare(<<<SQL
			UPDATE Etudiant
			SET numEtudiant=:num, 
				numRegime=:numR, 
				idEtape=:id, 
				nomEtudiant=:nom, 
				prenomEtudiant=:prenom, 
				mailEtudiant=:mail, 
				dateNaisEtudiant=:dateNais, 
				loginEtudiant=:login, 
				mdpEtudiant=:mdp, 
				dateIAEEtudiant=:dateIAE, 
				dateIAC2IEtudiant=:dateIAC2I,
				C2IValide=:C2IValide
			WHERE numEtudiant=:num
SQL
		);

		$q->bindValue(":num", 		$e->numEtudiant);
		$q->bindValue(":numR", 			$e->numRegime);
		$q->bindValue(":id",	 		$e->idEtape);
		$q->bindValue(":nom", 		$e->nomEtudiant);
		$q->bindValue(":prenom", 		$e->prenomEtudiant);
		$q->bindValue(":mail", 		$e->mailEtudiant);
		$q->bindValue(":dateNais", 		$e->dateNaisEtudiant);
		$q->bindValue(":login", 		$e->loginEtudiant);
		$q->bindValue(":mdp", 		$e->mdpEtudiant);
		$q->bindValue(":dateIAE", 		$e->dateIAEEtudiant);
		$q->bindValue(":dateIAC2I", 		$e->dateIAC2IEtudiant);
		$q->bindValue(":C2IValide",	$e->C2IValide);
		$q->execute();
	}

public function recuperationSHA1($code  /** Code contenant un condensat du mot de passe */) {
        // Préparation de la requête
        $stmt = $this->_myPDO->prepare(<<<SQL
    SELECT *
    FROM Etudiant
    WHERE SHA1(CONCAT(mdpEtudiant, :challenge, SHA1(loginEtudiant))) = :code
SQL
                ) ;

        $stmt->execute(array(
            ':challenge' => isset($_SESSION['challenge']) ? $_SESSION['challenge'] : '',
            ':code'      => $code)) ;
        // Test de réussite de la sélection
        $e = new Etudiant();
        if (($res = $stmt->fetch()) !== false) {
            // Chargement des données
            $e->numEtudiant = $res['NUMETUDIANT'];
			$e->nomEtudiant = $res['NOMETUDIANT'];
			$e->prenomEtudiant = $res['PRENOMETUDIANT'];
			$e->mailEtudiant = $res['MAILETUDIANT'];
			$e->dateNaisEtudiant = $res['DATENAISETUDIANT'];
			$e->loginEtudiant = $res['LOGINETUDIANT'];
			$e->mdpEtudiant = $res['MDPETUDIANT'];
			$e->dateIAEEtudiant = $res['DATEIAEETUDIANT'];
			$e->dateIAC2IEtudiant = $res['DATEIAC2IETUDIANT'];
			$e->numRegime = $res['NUMREGIME'];
			$e->idEtape = $res['IDETAPE'];
			$e->C2IValide = $res['C2IVALIDE'];
        }
        else {
            //throw new Exception("Login/pass incorrect") ;
            return NULL;
        }
        return $e;
    }	
}