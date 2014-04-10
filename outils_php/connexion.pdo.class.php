<?php

//****************************************************************************//
/// Singleton permettant de collecter des messages informatifs
final class Traceur {
    // Gestion de l'instance unique
    private static $_instance = null ; /// Objet Traceur

    // Atttributs de l'objet
    private        $_messages = array() ; /// Tableau des messages
    private        $_temps    = null ;    /// Instant de création de l'objet

    /// Constructeur privé
    private function __construct() {
        $this->_temps = microtime(true) ;
    }

    /// Interdire le clonage
    private function __clone() {
        throw new Exception("Clonage de " . __CLASS__ . " interdit !") ;
    }

    /// Accesseur à l'instance qui sera créée si nécessaire
    private static function donneInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self() ;
        }
        return self::$_instance ;
    }

    /// Méthode statique de collecte de messages
    public static function trace($msg) {
        $instance = self::donneInstance() ;
        $instance->_messages[] = $instance->duree() . " secondes : "  .$msg ;
    }

    /// Calcul du temps écoulé depuis la création du traceur
    private function duree() {
        return number_format(microtime(true) - $this->_temps, 4) ;
    }

    /// Méthode statique d'affichage des messages collectés
    public static function affiche($avant = "<!--", $apres = "-->") {
        $messages =  self::donneInstance()->_messages ;
        $traces = null ;
        if (count($messages)) {
            $traces .= "{$avant}\n" ;
            foreach ($messages as $m) {
                $traces .= "{$m}\n" ;
            }
            $traces .= "{$apres}\n" ;
        }

        return $traces ;
    }
} /* class Traceur */

//****************************************************************************//
/// Encapsulation de PDOStatement
final class myPDOStatement {
    /// L'objet PDOStatement
    private $pdoStatement ;

    /// Constructeur
    public function __construct($_pdoStatement /** L'objet PDOStatement */) {
        myPDO::msg("Construction PDOStatement") ;
        $this->pdoStatement = $_pdoStatement ;
    }

    /// Destructeur
    public function __destruct() {
        myPDO::msg("Destruction PDOStatement") ;
        $this->pdoStatement = null ;
    }

    /// Surcharge de toutes les méthodes indisponibles de myPDOStatement pour pouvoir appeler celles de PDOStatement
    public function __call($methodName      /** Nom de la méthode */,
                           $methodArguments /** Tableau des paramètres */) {
        // La méthode appelée fait-elle partie de la classe PDOStatement
        if (!method_exists($this->pdoStatement, $methodName))
            throw new Exception("PDOStatement::$methodName n'existe pas") ;
        // Message de debogage
        myPDO::msg("PDOStatement::".$methodName." (".var_export($methodArguments, true).")") ;
        // Appel de la méthode avec l'objet PDOStatement
        return call_user_func_array(array($this->pdoStatement, $methodName), $methodArguments) ;
    }

} /* class myPDOStatement */

//****************************************************************************//
/// Classe permettant de faire une connexion unique et automatique à la BD
final class myPDO
{
    /// Singleton
    private static $mypdo = null ;
    /// Message de debogage
    private static $debug = true ;
    /// Data Source Name
    private static $dsn   = null ;
    /// Utilisateur
    private static $user  = null ;
    /// Mot de passe
    private static $pass  = null ;
    /// Connexion à la base
    private        $pdo   = null ;

    /// Constructeur privé
    private function __construct() {
        self::msg("Demande construction PDO...") ;
        if (       is_null(self::$dsn)
                || is_null(self::$user)
                || is_null(self::$pass))
            throw new Exception("Construction impossible : les paramètres de connexion sont absents") ;
        // Etablir la connexion
        $this->pdo = new PDO(self::$dsn, self::$user, self::$pass) ;
        // Mise en place du mode "Exception" pour les erreurs PDO
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION) ;
        // Cas particulier de MySQL
        switch ($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME)){
            case 'mysql' :
                // Pour que cela fonctionne sur MySQL...
                $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true) ;
                // Passage du client MySQL en utf-8
                $this->pdo->query("SET CHARACTER SET 'utf8'") ;
            break ;
            case 'oci' :
                // Tris selon la langues française
                $this->pdo->query("ALTER SESSION SET NLS_SORT = FRENCH") ;
            break ;
        }
        self::msg("Construction PDO terminée") ;
    }

    /// Destructeur
    public function __destruct() {
        self::msg("Demande de destruction PDO...") ;
        // S'il y a une connexion établie...
        if (!is_null($this->pdo))
        {
            // ... il faut se deconnecter
            self::msg("Demande de déconnexion...") ;
            $this->pdo   = null ;
            self::$mypdo = null ;
            self::msg("Deconnexion effectuée") ;
        }
        self::msg("Destruction PDO terminée") ;
        echo Traceur::affiche() ;
    }

    /// Récupérer le singleton
    public static function donneInstance() {
        self::msg("Recherche de l'instance...") ;
        // Une instance est-elle disponible ?
        if (!isset(self::$mypdo))
            // Non, la créer
            self::$mypdo = new myPDO() ;
        self::msg("Instance trouvée") ;
        return self::$mypdo ;
    }

    /// Fixer les paramètres de connexion
    public static function parametres($_dsn, $_user, $_pass) {
        // self::msg("Demande de positionnement des paramètres de connexion...") ;
        self::$dsn  = $_dsn ;
        self::$user = $_user ;
        self::$pass = $_pass ;
        // self::msg("Positionnement des paramètres de connexion terminé") ;
    }

    /// Interdit le clonage du singleton
    public function __clone() {
        throw new Exception("Clonage de ".__CLASS__." interdit !") ;
    }

    /// Affichage de messages de contrôle
    public static function msg($m /** Le message */) {
        if (self::$debug)
            Traceur::trace($m) ;
    }

    /// Mise en marche du debogage
    public static function debug_on() {
        self::$debug = true ;
    }

    /// Arrêt du debogage
    public static function debug_off() {
        self::$debug = false ;
    }

    /// Surcharge de toutes les méthodes indisponibles de myPDO pour pouvoir appeler celles de PDO
    public function __call($methodName      /** Nom de la méthode */,
                                 $methodArguments /** Tableau des paramètres */) {
        // La méthode appelée fait-elle partie de la classe PDO
        if (!method_exists($this->pdo, $methodName))
            throw new Exception("PDO::$methodName n'existe pas") ;
        // Message de debogage
        self::msg("PDO::$methodName (".implode($methodArguments, ", ").")") ;
        // Appel de la méthode avec l'objet PDO
        $result = call_user_func_array(array($this->pdo, $methodName), $methodArguments) ;
        // Selon le nom de la méthode
        switch ($methodName)
        {
            // Cas 'prepare' ou 'query' => mise en place du fetchMode par tableau associatif
            case "prepare" :
            case "query" :
                $result->setFetchMode(PDO::FETCH_NAMED) ;
                // Retourne un objet myPDOStatement
                return new myPDOStatement($result) ;
            // Dans tous les autres cas
            default :
                // Retourne le resultat
                return $result ;
        }
    }
} /* class myPDO */


//****************************************************************************//
// Exemple de paramétrage pour une base de données particulière :
/*
require_once "connexion.pdo.v2.template.class.php" ;
myPDO::parametres('oci:dbname=bd11', 'scott', 'tiger') ;
*/

// Exemple d'utilisation :
/*
$stmt = myPDO::donneInstance()->query(<<<SQL
    SELECT *
    FROM dept
    ORDER BY 1
SQL
    ) ;
while ($ligne = $stmt->fetch()) {
    echo "{$ligne['DNAME']}<br>\n" ;
}
*/