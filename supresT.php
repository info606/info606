<?php
session_start();

require_once("/outils_php/autoload.php");
require_once("/outils_php/stringTools.php");
require_once("outils.php");

if(!verifConnexion("enseignant"))
{
	header('Location: nonautorise.php'); 
}

if(!(isset($_GET['name']) && !empty($_GET['name']) && isset($_GET['type']) && !empty($_GET['type'])))
{
	header('Location: index.php');
}
/* On vide les erreurs */
$_SESSION["erreurs"] = array();

switch($_GET['type'])
{
	/* Résultat */
	case 'r':
		if(loginFromFilename($_GET['name']) == $_SESSION['loginEnseignant'] || $_SESSION['admin'])
		{
			$resT = new ResultatTraiteur();
			$resT->suppression($_GET['name']);
		}
		else
		{
			$_SESSION['erreurs'][] = "Vous n'avez pas le droit de supprimer le fichier ".$_GET['name'];
		}
		header('Location: supres.php');
		break;
	case 'e':
		break;
}
