<?php
session_start();
require_once("/outils_php/autoload.php");
require_once("/outils_php/stringTools.php");
require_once("outils.php");

$menu = getMenuEnseignant();

if(!verifConnexion("enseignant"))
{
	header('Location: nonautorise.php'); 
}

$html = <<<HTML
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

$html.= <<<HTML
		<br>
		<div id="bandeau" class="row">
			<img class="col-md-4 col-md-offset-4" src="images/logo_c2i1.png" alt="c2i-1">	
		</div>
		<br><br>
		<div id="contenu">
			<br>
			<div class="row">
				$menu
				<div id="page" class="col-md-7 col-md-offset-1">
					<h2 class="h2">Supprimer des fichiers d'inscription</h2>
					<br>
HTML;
if(count($_SESSION['erreurs']) > 0)
{
	$html .= <<<HTML
		<fieldset>
			<legend>Erreurs</legend>
			<ul>
HTML;
	foreach ($_SESSION['erreurs'] as $value) {
		$html .= "<li>".$value."</li>";	
	}
	$html .= <<<HTML
			</ul>
		</fieldset>
HTML;
}
$html.= <<<HTML
	<table id='tabSuppressionInsc' class='table table-bordered'><tr><td>Nom du fichier<td>Actions
HTML;
$path = $_SERVER['DOCUMENT_ROOT']."/info606/donnees/resultats/";
$files = scandir($path , SCANDIR_SORT_NONE);
foreach ($files as $key => $value) {
	if(is_file($path.$value))
	{
		if($_SESSION['loginEnseignant'] == loginFromFilename($value) || $_SESSION['admin'])
		{
			$html.="<tr><td>".$value."<td><a href='supresT.php?name=".$value."&type=r'><button type='button' class='btn btn-info'>Supprimer</button></a>";
		}
	}
}

if($_SESSION["admin"])
{
	$html .= <<<HTML
		
HTML;
}
$html .= <<<HTML
					</table>
				</div>

			</div>
<br><br>
		</div>

HTML;

$html.=<<<HTML
	</body>
</html>
HTML;

echo $html;