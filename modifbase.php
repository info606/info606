<?php

session_start();
require_once("/outils_php/autoload.php");
require_once("outils.php");

$menu = getMenuEnseignant();

if(verifConnexion("enseignant")){
	if(isset($_POST["action"])){
		if($_POST["action"] == "modifEtudiant"){
			echo $_POST["numEtudiant"]." - ".$_POST["nomEtudiant"]." - ".$_POST["prenomEtudiant"]." - ".$_POST["mailEtudiant"]." - ".dateFR2US($_POST["naisEtudiant"])." - ".$_POST["loginEtudiant"]." - ".$_POST["naisEtudiant"]." - ".dateFR2US($_POST["dateIAEEtudiant"])." - ".dateFR2US($_POST["dateIAC2IEtudiant"])." - ".$_POST["regimeEtudiant"]." - ".$_POST["etapeEtudiant"];
			$etudiantControleur = new EtudiantControleur();
			$e = new Etudiant($_POST["numEtudiant"],$_POST["nomEtudiant"],$_POST["prenomEtudiant"],$_POST["mailEtudiant"],dateFR2US($_POST["naisEtudiant"]),$_POST["loginEtudiant"],$_POST["mdpEtudiant"],dateFR2US($_POST["dateIAEEtudiant"]),dateFR2US($_POST["dateIAC2IEtudiant"]),$_POST["regimeEtudiant"],$_POST["etapeEtudiant"]);
			print_r($e);
			$etudiantControleur->etudiantManager->maj($e);

			header('Location: gestionbase.php');
		}
		else if($_POST["action"] == "modifEnseignant"){
			$enseignantControleur = new EnseignantControleur();
			$e = new Enseignant($_POST["numEnseignant"],$_POST["nomEnseignant"],$_POST["prenomEnseignant"],$_POST["loginEnseignant"],$_POST["mdpEnseignant"]);
			$enseignantControleur->enseignantManager->maj($e);

			header('Location: gestionbase.php');
		}
		else if($_POST["action"] == "modifEpreuve"){
			$epreuveControleur = new EpreuveControleur();
			$e = new Epreuve($_POST["numEpreuve"],$_POST["libEpreuve"],$_POST["composanteEpreuve"]);
			$epreuveControleur->epreuveManager->maj($e);

			header('Location: gestionbase.php');
		}
		else if($_POST["action"] == "creaEtudiant"){
			//echo $_POST["numEtudiant"]." - ".$_POST["nomEtudiant"]." - ".$_POST["prenomEtudiant"]." - ".$_POST["mailEtudiant"]." - ".dateFR2US($_POST["naisEtudiant"])." - ".$_POST["loginEtudiant"]." - ".$_POST["naisEtudiant"]." - ".dateFR2US($_POST["dateIAEEtudiant"])." - ".dateFR2US($_POST["dateIAC2IEtudiant"])." - ".$_POST["regimeEtudiant"]." - ".$_POST["etapeEtudiant"];
			$etudiantControleur = new EtudiantControleur();
			$e = new Etudiant($_POST["numEtudiant"],$_POST["nomEtudiant"],$_POST["prenomEtudiant"],$_POST["mailEtudiant"],dateFR2US($_POST["naisEtudiant"]),$_POST["loginEtudiant"],$_POST["naisEtudiant"],dateFR2US($_POST["dateIAEEtudiant"]),dateFR2US($_POST["dateIAC2IEtudiant"]),$_POST["regimeEtudiant"],$_POST["etapeEtudiant"]);
			//print_r($e);
			$etudiantControleur->etudiantManager->ajouter($e);

			header('Location: gestionbase.php');
		}
		else if($_POST["action"] == "creaEnseignant"){
			$enseignantControleur = new EnseignantControleur();
			$e = new Enseignant($_POST["numEnseignant"],$_POST["nomEnseignant"],$_POST["prenomEnseignant"],$_POST["loginEnseignant"],$_POST["mdpEnseignant"]);
			$enseignantControleur->enseignantManager->ajouter($e);

			header('Location: gestionbase.php');
		}
		else if($_POST["action"] == "creaEpreuve"){
			$epreuveControleur = new EpreuveControleur();
			$e = new Epreuve("",$_POST["libEpreuve"],$_POST["composanteEpreuve"]);
			$epreuveControleur->epreuveManager->ajouter($e);

			header('Location: gestionbase.php');
		}
	}
	else{
		header('Location: gestionbase.php'); 
	}
}
else{
	header('Location: nonautorise.php'); 
}