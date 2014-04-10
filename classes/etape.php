<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class Etape
{
    private $idEtape;
	private $codeEtape;
	private $idCursus; /* Clé etrangère */
	private $numComposante; /* Clé étrangère */
	private $libCourtEtape;
	private $versionEtape;
	private $libLongEtape;

	public function __construct($code="", $libCourt="", $versionEtape="", $libLong="", $idCursus="", $numComp="")
	{
		$this->codeEtape = $code;
		$this->idCursus = $idCursus; /* Clé etrangère */
		$this->numComposante = $numComp;
		$this->libCourtEtape = $libCourt;
		$this->versionEtape = $versionEtape;
		$this->libLongEtape = $libLong;
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