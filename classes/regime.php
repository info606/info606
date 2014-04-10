<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];

require_once($rootPath."/info606/outils_php/autoload.php");

class Regime
{
	private $numRegime;
	private $libRegime;

	public function __construct($lib="")
	{
		$this->libRegime = $lib;
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