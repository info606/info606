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
	/* On vide les erreurs */
	$_SESSION["erreurs"] = array();

echo <<<HTML
<!DOCTYPE html>
<html lang="en">
	<head>
		<link type="text/css" rel="stylesheet" href="css/bootstrap.css">
		<link type="text/css" rel="stylesheet" href="css/style.css">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>c2i-1</title>
	</head>
	<body class="container-fluid">
HTML;

	$compControleur = new ComposanteControleur();
	$composante = $compControleur->composanteManager->recupererParNum($_SESSION["numComposante"]);
	$dossier = "donnees/".$_POST["type"]."/";

	$nomFichier = $composante->libComposante."_".$_SESSION["loginEnseignant"]."_".date("dmY").".csv";
	$extension = strrchr($_FILES["fichier"]["name"],'.');
	if($extension != ".csv")
	{
		$_SESSION['erreurs'][] = "Mauvaise extension !";
	}
	if(filesize($_FILES["fichier"]["tmp_name"]) > 2000000)
	{
		$_SESSION['erreurs'][] = "Fichier trop volumineux";
	}

	if((count($_SESSION['erreurs'] != 0)) && move_uploaded_file($_FILES["fichier"]["tmp_name"], $dossier.$nomFichier))
	{
		switch($_POST["type"])
		{
			case "resultats":
				$resT = new ResultatTraiteur();
				$resT->traiter($nomFichier);
				// $resT->maj(); // Test de la mise à jour
				break;
			case "etapes":
				$etaT = new EtapeTraiteur();
				$etaT->traiter($nomFichier);
				break;
			case "etudiants":
				$insT = new InscriptionTraiteur();
				$insT->traiter($nomFichier);
				break;
		}
	}
	else
	{
		$_SESSION['erreurs'][] = "Impossible d'uploader le fichier.";
	}

	var_dump($_SESSION['erreurs']);


echo <<<HTML
	</body>
</html>
HTML;
}
else
{
	echo "Haha";
}