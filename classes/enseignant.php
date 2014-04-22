<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class Enseignant
{
	private $numEnseignant;
	private $nomEnseignant;
	private $prenomEnseignant;
	private $loginEnseignant;
	private $mdpEnseignant;
    private $numComposante;
    private $admin;

	public function __construct($num="",
                                $nom="",
								$prenom="", 
								$login="", 
								$mdp="",
                                $numComposante="",
                                $admin=false)
	{
        $this->numEnseignant = $num;
		$this->nomEnseignant = $nom;
		$this->prenomEnseignant = $prenom;		
		$this->loginEnseignant = $login;
		$this->mdpEnseignant = $mdp;
        $this->numComposante = $numComposante;
        $this->admin = $admin;	
	}

	 public function __get($attribut)
    {
        if (property_exists($this, $attribut))
        {
            return $this->$attribut;
        }
        else
        {
            throw new Exception("Attribut : " . $attribut . " innexistant dans la classe : " . get_class($this), 404);
        }
    }

    public function __set($attribut, $value)
    {
        if (property_exists($this, $attribut))
        {
            $this->$attribut = $value;
        }
        else
        {
            throw new Exception("Attribut : " . $attribut . " innexistant dans la classe : " . get_class($this), 404);
        }

        return $this;
    }

    public function __toString()
	{
		return $this->prenomEnseignant . " " . $this->nomEnseignant;
	}

		////////////////////////////////////////////////////////////////////////////////
    /// Déconnecter l'utilisateur
    static public function deconnexion() {
        self::demarrerSession() ;
        session_destroy() ;
    }

    ////////////////////////////////////////////////////////////////////////////////
    /// Un utilisateur est-il connecté ?
    static public function estConnecte() {
        self::demarrerSession() ;
        return isset($_SESSION['connecte']) && $_SESSION['connecte'] == 'oui' ;
    }

    ////////////////////////////////////////////////////////////////////////////////
    /// Production d'un formulaire de connexion contenant un challenge
    static public function formulaireConnexionSHA1($action /** URL cible du formulaire */) {
        $texte_par_defaut = 'login?' ;
        // Mise en place de la session
        self::demarrerSession() ;
        // Mémorisation d'un challenge dans la session
        $_SESSION['challenge'] = self::codeAleatoire(16) ;
        // Si un cookie 'login' existe, mettre sa valeur dans le champ login
        $value = isset($_COOKIE['login']) ? " value={$_COOKIE['login']}" : "" ;
        // Le formulaire avec le code JavaScript permettant le hachage SHA1
        // Le retour attendu par le serveur est SHA1(SHA1(pass)+challenge+SHA1(login))
        return <<<HTML
<script type='text/javascript' src='http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha1.js'></script>
<script type='text/javascript'>
function crypter(f, challenge) {
    if (f.login.value.length && f.pass.value.length) {
        f.code.value = CryptoJS.SHA1(CryptoJS.SHA1(f.pass.value)+challenge+CryptoJS.SHA1(f.login.value)) ;
        f.login.value = f.pass.value = '' ;
        return true ;
    }
    return false ;
}
</script>
<!--
Le formulaire est envoyé selon la méthode GET à des fins de compréhension.
Il faut utiliser la méthode POST dans la pratique.
-->
<form name='auth' action='$action' method='POST' onSubmit="return crypter(this, '{$_SESSION['challenge']}')">
<table>
    <tr><td>login&nbsp;:<td><input type='text' style='width:50px;'name='login' {$value}
                              onClick="if (this.value == '$texte_par_defaut') this.value = ''"
                              onFocus="if (this.value == '$texte_par_defaut') this.value = ''">
        <td>pass&nbsp;:<td><input type='password' name='pass'  >
            <input type='hidden' name='code'>
    <td colspan='2'><input type='submit'   value='Connexion'>
</table>
</form>
HTML;
    }

    ////////////////////////////////////////////////////////////////////////////////
    /// Validation de la connexion de l'Utilisateur
    public function connexionSHA1($code  /** Code contenant un condensat du mot de passe */) {
        require_once("outils_php/connexion.pdo.class.php");
        myPDO::parametres('mysql:host=127.0.0.1;dbname=espritdequipe', 'root', '') ;
        // Mise en place de la session
        self::demarrerSession() ;
        // Préparation de la requête
        $stmt = myPDO::donneInstance()->prepare(<<<SQL
    SELECT e.numEnseignant, e.nomEnseignant, e.prenomEnseignant, e.loginEnseignant
    FROM Enseignant e
    WHERE SHA1(CONCAT(e.mdpEnseignant, :challenge, SHA1(e.loginEnseignant))) = :code
SQL
                ) ;

        $stmt->execute(array(
            ':challenge' => isset($_SESSION['challenge']) ? $_SESSION['challenge'] : '',
            ':code'      => $code)) ;
        // Test de réussite de la sélection
        if (($utilisateur = $stmt->fetch()) !== false) {
            // Chargement des données
            $this->numEnseignant = $utilisateur['numEnseignant'];
			$this->nomEnseignant = $utilisateur['nomEnseignant'];
			$this->prenomEnseignant = $utilisateur['prenomEnseignant'];
			$this->loginEnseignant = $utilisateur['loginEnseignant'];
            // Mise en place d'un cookie contenant le login
            setcookie('login', // Nom du cookie
                      $this->loginEnseignant, // Sa valeur
                      time()+15*24*3600, // Sa date d'expiration
                      dirname($_SERVER['PHP_SELF']), // Son chemin de validité
                      mb_ereg("^.+\..+\..+$", $_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null) ; // Son domaine de validité
        }
        else {
            throw new Exception("Login/pass incorrect") ;
        }
    }

    ////////////////////////////////////////////////////////////////////////////////
    /// Sauvegarde de l'objet Utilisateur dans la session
    public function sauverDansSession() {
        // Mise en place de la session
        self::demarrerSession() ;
        // Mémorisation de l'Utilisateur
        $_SESSION['utilisateur'] = $this ;
    }

    ////////////////////////////////////////////////////////////////////////////////
    /// Effacement de l'objet Utilisateur contenu dans la session
    static public function detruireSession() {
        // Mise en place de la session
        self::demarrerSession() ;
        // Mémorisation de l'Utilisateur
        unset($_SESSION['utilisateur']) ;
    }

    ////////////////////////////////////////////////////////////////////////////////
    /// Lecture de l'objet Utilisateur dans la session
    static public function lireDepuisSession() {
        // Mise en place de la session
        self::demarrerSession() ;
        // La variable de session existe ?
        if (isset($_SESSION['utilisateur'])) {
            // Lecture de la variable de session
            $u = $_SESSION['utilisateur'] ;
            // Est-ce un objet et un objet du bon type ?
            if (is_object($u) && get_class($u) == get_class()) {
                // OUI ! on le retourne
                return $u ;
            }
            else {
                // NON ! on retourne null
                return null ;
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////////////
    /// Démarrer une session
    static private function demarrerSession() {
        // Vision la plus contraignante et donc la plus fiable
        // Si les en-têtes ont deja été envoyés, c'est trop tard...
        if (headers_sent())
            throw new Exception("Impossible de démarrer une session si les en-têtes HTTP ont été envoyés") ;
        // Si la session n'est pas demarrée, le faire
        if (!session_id()) session_start() ;

        // Vision la plus moins contraignante qui peut amener des comportements changeants
        // Si la session n'est pas demarrée, le faire
        /*
        if (!session_id()) {
            // Si les en-têtes ont deja été envoyés, c'est trop tard...
            if (headers_sent())
                throw new Exception("Impossible de démarrer une session si les en-têtes HTTP ont été envoyés") ;
            // Démarrer la session
            session_start() ;
        }
        */
    }


    ////////////////////////////////////////////////////////////////////////////////
    /// Production d'un code aléatoire (minuscule, majuscule et chiffre)
    public static function codeAleatoire($taille /** Taille de la chaîne aléatoire */) {
        $c = '' ;
        for ($i=0; $i<$taille; $i++) {
            switch (rand(0, 2)) {
                case 0 :
                    $c .= chr(rand(ord('A'), ord('Z'))) ;
                    break ;
                case 1 :
                    $c .= chr(rand(ord('a'), ord('z'))) ;
                    break ;
                case 2 :
                    $c .= chr(rand(ord('1'), ord('9'))) ;
                    break ;
            }
        }
        return $c ;
    }
}