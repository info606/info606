<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class Validation
{
	private $idValidation;
	private $numEnseignant; /* Clé étrangère */
    private $numEtudiant; /* Clé étrangère */
    private $idEpreuve; /* Clé étrangère */
    private $dateValidation;
    private $anneeValidation;
    private $valeurValidation;

	public function __construct($date="", $numEnseignant="", $numEtudiant="", $idEpreuve="", $valeur = -1)
	{
        $this->numEnseignant = $numEnseignant; /* Clé étrangère */
        $this->numEtudiant = $numEtudiant; /* Clé étrangère */
        $this->idEpreuve = $idEpreuve; /* Clé étrangère */
        $this->dateValidation = $date;
        $this->valeurValidation = $valeur;
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
		/* TO-DO */
	}
}