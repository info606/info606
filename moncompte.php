<?php

session_start();
require_once("/outils_php/autoload.php");
require_once("outils.php");

$menu = getMenuEtudiant();
$menu2 = getMenuEnseignant();
$message ="";


if(verifConnexion("all")){

	if($_SESSION["typePersonne"]=="etudiant"){

if(isset($_POST["oldpwd"]) && isset($_POST["newpwd"]) && isset($_POST["newpwd2"])){
	$controleurEtu = new EtudiantControleur();
	$etudiant = $controleurEtu->etudiantManager->recupererParNum($_SESSION["numEtudiant"]);
	if($etudiant->mdpEtudiant === sha1($_POST["oldpwd"]) && $_POST["newpwd"] === $_POST["newpwd2"]){
		$etudiant->mdpEtudiant=sha1($_POST["newpwd"]);
		$controleurEtu->etudiantManager->maj($etudiant);
		$message="Modification bien prise en compte.";
	}
	else if($etudiant->mdpEtudiant != sha1($_POST["oldpwd"])){
		$message="L'ancien mot de passe ne concorde pas!";
	}
	else{
		$message="Le nouveau mot de passe ne concorde pas!";
	}
}

$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
	<head>
		<link type="text/css" rel="stylesheet" href="css/bootstrap.css">
		<link type="text/css" rel="stylesheet" href="css/style.css">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Mon compte</title>
	</head>
	<body class="container-fluid">
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
					<h2 class="h2">Mon compte</h2>
					<br>
					<h4 class="h4">$message</h4>
					<table>
						<h3 class="h3">Vos informations personnelles<h3><br>
						<tr><td>Numéro étudiant<td><input class="form-control" id="disabledInput" type="text" placeholder="{$_SESSION["numEtudiant"]}" disabled>
						<tr><td>Nom<td><input class="form-control" id="disabledInput" type="text" placeholder="{$_SESSION["nomEtudiant"]}" disabled>
						<tr><td>Prénom<td><input class="form-control" id="disabledInput" type="text" placeholder="{$_SESSION["prenomEtudiant"]}" disabled>
						<tr><td>E-mail<td><input class="form-control" id="disabledInput" type="text" placeholder="{$_SESSION["mailEtudiant"]}" disabled>
						<tr><td>Date de naissance<td><input class="form-control" id="disabledInput" type="text" placeholder="{$_SESSION["dateNaisEtudiant"]}" disabled>
						<tr><td>Login<td><input class="form-control" id="disabledInput" type="text" placeholder="{$_SESSION["loginEtudiant"]}" disabled>
						<tr><td>Date inscription c2i-1<td><input class="form-control" id="disabledInput" type="text" placeholder="{$_SESSION["dateIAC2IEtudiant"]}" disabled>
					</table>
						<br><br>
					<table>
						<h3 class="h3">Changement de mot de passe</h3><br>
						<form action="moncompte.php" method="POST">
						<tr><td>Ancien mot de passe<td><input name="oldpwd" type="password" class="form-control" placeholder="Ancien mot de passe">
						<tr><td>Nouveau mot de passe<td><input name="newpwd" type="password" class="form-control" placeholder="Nouveau mot de passe">
						<tr><td>Confirmez le nouveau mot de passe<td><input name="newpwd2" type="password" class="form-control" placeholder="Confirmez">
						<br>
						<tr><td><td><button type="submit" class="btn btn-default">Valider le changement</button>
						</form>
					</table>
					<br><br>
				</div>
			</div>
			<br><br><br>
		</div>
	</body>
</html>
HTML;

echo $html;
		}
		else if($_SESSION["typePersonne"]=="enseignant"){

if(isset($_POST["oldpwd"]) && isset($_POST["newpwd"]) && isset($_POST["newpwd2"])){
	$controleurEns = new EnseignantControleur();
	$ens = $controleurEns->enseignantManager->recupererParNum($_SESSION["numEnseignant"]);
	if($ens->mdpEnseignant === sha1($_POST["oldpwd"]) && $_POST["newpwd"] === $_POST["newpwd2"]){
		$ens->mdpEnseignant=sha1($_POST["newpwd"]);
		$controleurEns->enseignantManager->maj($ens);
		$message="Modification bien prise en compte.";
	}
	else if($ens->mdpEnseignant != sha1($_POST["oldpwd"])){
		$message="L'ancien mot de passe ne concorde pas!";
	}
	else{
		$message="Le nouveau mot de passe ne concorde pas!";
	}
}

$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
	<head>
		<link type="text/css" rel="stylesheet" href="css/bootstrap.css">
		<link type="text/css" rel="stylesheet" href="css/style.css">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Mon compte</title>
	</head>
	<body class="container-fluid">
		<br>
		<div id="bandeau" class="row">
			<img class="col-md-4 col-md-offset-4" src="images/logo_c2i1.png" alt="c2i-1">	
		</div>
		<br><br>
		<div id="contenu">
			<br>
			<div class="row">
				$menu2
				<div id="page" class="col-md-7 col-md-offset-1">
					<h2 class="h2">Mon compte</h2>
					<br>
					<h4 class="h4">$message</h4>
					<table>
						<h3 class="h3">Vos informations personnelles<h3><br>
						<tr><td>Nom<td><input class="form-control" id="disabledInput" type="text" placeholder="{$_SESSION["nomEnseignant"]}" disabled>
						<tr><td>Prénom<td><input class="form-control" id="disabledInput" type="text" placeholder="{$_SESSION["prenomEnseignant"]}" disabled>
						<tr><td>Login<td><input class="form-control" id="disabledInput" type="text" placeholder="{$_SESSION["loginEnseignant"]}" disabled>
						<tr><td>Composante<td><select disabled name="compEnseignant" class="form-control" id="inputcomposante3">
HTML;
		$composanteControleur = new ComposanteControleur();
		$composantes = $composanteControleur->composanteManager->recupererTout();
		foreach ($composantes as $c) {
			if($_SESSION['numComposante'] != $c->numComposante)
			{
				$html.="<option value='{$c->numComposante}'>$c->libComposante</option>";	
			}
			else
			{
				$html.="<option selected value='{$c->numComposante}'>$c->libComposante</option>";
			}
			
		}
$html.=<<<HTML
							</select>
					</table>
						<br><br>
					<table>
						<h3 class="h3">Changement de mot de passe</h3><br>
						<form action="moncompte.php" method="POST">
						<tr><td>Ancien mot de passe<td><input name="oldpwd" type="password" class="form-control" placeholder="Ancien mot de passe">
						<tr><td>Nouveau mot de passe<td><input name="newpwd" type="password" class="form-control" placeholder="Nouveau mot de passe">
						<tr><td>Confirmez le nouveau mot de passe<td><input name="newpwd2" type="password" class="form-control" placeholder="Confirmez">
						<br>
						<tr><td><td><button type="submit" class="btn btn-default">Valider le changement</button>
						</form>
					</table>
					<br><br>
				</div>
			</div>
			<br><br><br>
		</div>
	</body>
</html>
HTML;

echo $html;
		}

}
else{
	header('Location: nonautorise.php'); 
}
