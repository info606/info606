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
					<h2 class="h2">Importer des données</h2>
					<br>
HTML;
if(count($_SESSION['erreurs']) > 0)
{
	$formu .= <<<HTML
		<fieldset>
			<legend>Erreurs</legend>
			<ul>
HTML;
	foreach ($_SESSION['erreurs'] as $value) {
		$formu .= "<li>".$value."</li>";	
	}
$formu .= <<<HTML
			</ul>
		</fieldset>
HTML;
}
$dateDeb = date('Y')-3;
$dateMax = date('Y')+1;
$date = date('Y');
$formu .= <<<HTML
					<form method="POST" action="upload.php" enctype="multipart/form-data">
						<fieldset>
							<legend>Fichier</legend>
							<input type="file" name="fichier"/>
							<input type="hidden" name="MaX_FILE_SIZE" value="2000000"/>
						</fieldset>

						<br><br>

						<fieldset>
							<legend>Source des données</legend>
							<input type="radio" id="res" name="type" value="resultats"/><label for="res"> Résultats</label><br>
							<input type="radio" id="etape" name="type" value="etapes"/><label for="etape"> Etapes</label><br>
							<input type="radio" id="etud" name="type" value="etudiants"/><label for="etud"> Etudiants</label><br>
						</fieldset>

						<br><br>

						<fieldset>
							<legend>Date des données (Année scolaire)</legend>
							<input type="number" name="annee" min="$dateDeb" max="$dateMax" value="$date"/>
						</fieldset>

						<br><br>

						<fieldset>
							<legend>Validation</legend>
HTML;
if($_SESSION["admin"])
{
	$formu .= <<<HTML
		<input type="checkbox" id="definitif" name="definitif"/><label for="definitif">Import définitif</label><br><br>
HTML;
}
$formu .= <<<HTML
								<input type="submit" class="btn btn-default" value="Envoyer"/>
						<fieldset>
					</form>

					<br><br>

				</div>

				

			</div>
			<br><br>
		</div>
HTML;

$html .= $formu;
$html.=<<<HTML
	</body>
</html>
HTML;

echo $html;