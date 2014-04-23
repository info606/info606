<?php
session_start();
require_once("/outils_php/autoload.php");
require_once("outils.php");

$menu = getMenuEtudiant();
$menu2 = getMenuEnseignant();

//if(isset($_SESSION) && isset($_SESSION["numEtudiant"]) && !empty($_SESSION["numEtudiant"])){
if(verifConnexion("all")){
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
		<br>
		<div id="bandeau" class="row">
			<img class="col-md-4 col-md-offset-4" src="images/logo_c2i1.png" alt="c2i-1">	
		</div>
		<br><br>
		<div id="contenu">
			<br>
			<div class='row'>
HTML;

if($_SESSION["typePersonne"]=="etudiant")
	$html.="<h4 class='h4 col-md-3 col-md-offset-1'>Bonjour ".$_SESSION["prenomEtudiant"]." ".$_SESSION["nomEtudiant"]."</h4>";
else
	$html.="<h4 class='h4 col-md-3 col-md-offset-1'>Bonjour ".$_SESSION["prenomEnseignant"]." ".$_SESSION["nomEnseignant"]."</h4>";

$html .= <<<HTML
				
				<form action="moncompte.php" method="POST">
					<button type="submit" class="btn btn-primary btn-sm col-md-1 col-md-offset-5">Mon compte</button>
				</form>
				<form action="deconnexion.php" method="POST">
					<button type="submit" class="btn btn-danger btn-sm col-md-1">Deconnexion</button>
				</form>
			</div>

			<br><br><br>

			<div class='row'>
HTML;

if($_SESSION["typePersonne"]=="etudiant")
	$html .= $menu;
else
	$html.=$menu2;

$html.=<<<HTML
				<div id="page" class="col-md-7 col-md-offset-1">
					<h5 class='h4'>Vous êtes connecté sur votre espace personnel du c2i-1</h5>
				</div>

			</div>

			<br><br><br>
		</div>

	</body>
</html>
HTML;

echo $html;
}
else{

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
		<div id="bandeau" class="row">
			<br>
			<img id="urca" class="col-md-4 col-md-offset-1" src="images/urca.png" amt="URCA">
			<img class="col-md-4 col-md-offset-2" src="images/logo_c2i1.png" alt="c2i-1">
			
		</div>

		<div class="row">
			<br><br>
			<h1 class="h1">Page d'authentification</h1>
HTML;

	if(isset($_GET['msg']) && $_GET['msg']=="error"){
		$html .= "<p id='msgError'>Une erreur est survenue, veuillez recommencer.</p>";
	}
	else if(isset($_GET['msg']) && $_GET['msg']=="unknown"){
		$html .= "<p id='msgError'>Mauvais login ou mot de passe.</p>";
	}

$html .= <<<HTML
			<br><br>
			<form class="form-horizontal" role="form" name="formulaire" action="connexion.php" method="POST">
				<div class="form-group">
					<label for="inputEmail3" class="col-sm-2 control-label">Login</label>
					<div class="col-sm-10">
						<input name="login" style="width: 50%;" type="text" class="form-control" placeholder="Login">
					</div>
				</div>
				<div class="form-group">
					<label for="inputPassword3" class="col-sm-2 control-label">Mot de passe</label>
					<div class="col-sm-10">
						<input name="password" style="width: 50%;" type="password" class="form-control" id="inputPassword3" placeholder="Mot de passe">
					</div>
				</div>
				<div class="form-group">
    				<div class="col-sm-offset-2 col-sm-10">
      					<div class="checkbox">
        					<label>
          						<input name='enseignant' type="checkbox"> Espace enseignant
        					</label>
      					</div>
    				</div>
  				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-4">
						<button type="submit" class="btn btn-default">Se connecter</button>
						<a class="col-sm-offset-1 info_bulle">Première connexion?
							<span>
								Vous devez entrez:<br>
								- login : votre login de bureau virtuel<br>
								- mot de passe : votre date de naissance sous la forme JJ/MM/AAAA
							</span>
						</a>
					</div>
				</div>
			</form>
		</div>
	</body>
</html>
HTML;

echo $html;

}