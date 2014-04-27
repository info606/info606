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
}