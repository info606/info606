<?php
session_start();
require_once("/outils_php/autoload.php");
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

$formu = <<<HTML
		<div id="bandeau" class="row">
			<img class="col-md-4 col-md-offset-4" src="images/logo_c2i1.png" alt="c2i-1">	
		</div>
		<div id="contenu">
			<br>
			<div class="row">
				$menu
				<div id="page" class="col-md-7 col-md-offset-1">
					<h2 class="h2">Importer des données</h2>
					<form method="POST" action="upload.php" enctype="multipart/form-data">
						<fieldset>
							<legend>Fichier</legend>
							<input type="file" name="fichier"/>
							<input type="hidden" name="MaX_FILE_SIZE" value="2000000"/>
						</fieldset>
						<br>
						<fieldset>
							<legend>Source des données</legend>
							<input type="radio" id="res" name="type" value="resultats"/><label for="res">Résultats</label>
							<input type="radio" id="etud" name="type" value="etudiants"/><label for="etud">Etudiants</label>
						</fieldset>
HTML;
if($_SESSION["admin"])
{
	$formu .= <<<HTML
		<input type="checkbox" id="definitif" name="definitif"/><label for="definitif">Import définitif</label><br>
HTML;
}
$formu .= <<<HTML
						<fieldset>
							<legend>Validation</legend>
								<input type="submit" value="envoyer"/>
						<fieldset>
					</form>
				</div>
			</div>
		</div>
HTML;

$html .= $formu;
$html.=<<<HTML
	</body>
</html>
HTML;

echo $html;