<?php

session_start();
require_once("/outils_php/autoload.php");
require_once("outils.php");

if(!verifConnexion("enseignant"))
{
	header('Location: nonautorise.php'); 
}

if(isset($_FILES["fichier"]) && isset($_POST["type"]) && !empty($_POST["type"]))
{
	$compControleur = new ComposanteControleur();
	$composante = $compControleur->composanteManager->recupererParNum($_SESSION["numComposante"]);
	$dossier = "donnees/".$_POST["type"]."/";

	$nomFichier = $composante->libComposante."_".$_SESSION["loginEnseignant"]."_".date("dmY").".csv";
	$extension = strrchr($_FILES["fichier"]["name"],'.');
	if($extension != ".csv")
	{
		$erreur = "Mauvaise extension !";
	}
	if(filesize($_FILES["fichier"]["tmp_name"]) > 2000000)
	{
		$erreur = "Fichier trop volumineux";
	}

	if(!isset($erreur) && move_uploaded_file($_FILES["fichier"]["tmp_name"], $dossier.$nomFichier))
	{
			
		switch($_POST["type"])
		{
			case "resultat":
				/* Traitement du fichier de résultats */
				break;
			case "etape":
				/* Traitement du fichier d'étape */
				break;
			case "etudiant":
				/* Traitement du fichier d'étudiants */
				break;
		}
	}
	else
	{
		echo $erreur;
	}
}