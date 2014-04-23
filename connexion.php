<?php
session_start();

header('Refresh: 3; URL=index.php'); 

require_once("/outils_php/autoload.php");
require_once("/outils_php/connexion.pdo.class.php");
require_once("outils.php");

$login="";
$pwd="";

if(!isset($_POST["login"]) && empty($_POST["login"]) && !isset($_POST["password"]) && empty($_POST["password"])){
	header('Location: index.php?msg=error');
}
else{
	 
	$login=$_POST["login"];
	$pwd=$_POST["password"];

	/* S'il s'agit d'un enseignant */
	if(isset($_POST["enseignant"]) && $_POST["enseignant"] == "on"){
		$controleurEns = new EnseignantControleur();
		$ens = new Enseignant("","","",$login,$pwd);
		if(!$controleurEns->enseignantManager->exists($ens))
			header('Location: index.php?msg=unknown'); 
		else{
			$ensRecup = $controleurEns->enseignantManager->recupererParLogin($login);

			if($ensRecup->mdpEnseignant != $_POST["password"]){
				header('Location: index.php?msg=unknown');
			}
			else{
				$_SESSION["typePersonne"]="enseignant";
				$_SESSION["numEnseignant"]=$ensRecup->numEnseignant;
				$_SESSION["nomEnseignant"]=$ensRecup->nomEnseignant;
				$_SESSION["prenomEnseignant"]=$ensRecup->prenomEnseignant;
				$_SESSION["loginEnseignant"]=$ensRecup->loginEnseignant;
				$_SESSION["numComposante"]=$ensRecup->numComposante;
				$_SESSION["admin"] = ($ensRecup->admin==1)?true:false;
				$_SESSION["erreurs"]=array();

$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="stylesheet" href="css/bootstrap.css">
		<link rel="stylesheet" href="css/style.css">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>c2i-1 Connexion</title>
	</head>
	<body class="container-fluid">
		<div id="bandeau" class="row">
			<img class="col-md-4 col-md-offset-4" src="images/logo_c2i1.png" alt="c2i-1">
		</div>
		<div id="connexion">
			<br>
			<p>Vous êtes maintenant connecté.<br>Vous allez être redirigé vers votre espace personnel.<br>Si ce n'est pas le cas, <a href="index.php">cliquez-ici</a>
			</p>
			<br>
		</div>
	</body>
</html>
HTML;
	
				echo $html;

			}
		}
	}
	else{ /* Sinon il s'agit d'un etudiant */
		$controleurEtu = new EtudiantControleur();
		$etudiant = new Etudiant("","","","","",$login,$pwd);
		if(!$controleurEtu->etudiantManager->existsParLogin($etudiant))
			header('Location: index.php?msg=unknown'); 
		else{

			$etudiantRecup = $controleurEtu->etudiantManager->recupererParLogin($login);

			if($etudiantRecup->mdpEtudiant != $_POST["password"]){
				header('Location: index.php?msg=unknown');
			}
			else{

				$_SESSION["typePersonne"]="etudiant";
				$_SESSION["numEtudiant"]=$etudiantRecup->numEtudiant;
				$_SESSION["nomEtudiant"]=$etudiantRecup->nomEtudiant;
				$_SESSION["prenomEtudiant"]=$etudiantRecup->prenomEtudiant;
				$_SESSION["mailEtudiant"]=$etudiantRecup->mailEtudiant;
				$_SESSION["dateNaisEtudiant"]=dateUS2FR($etudiantRecup->dateNaisEtudiant);
				$_SESSION["loginEtudiant"]=$etudiantRecup->loginEtudiant;
				$_SESSION["dateIAEEtudiant"]=dateUS2FR($etudiantRecup->dateIAEEtudiant);
				$_SESSION["dateIAC2IEtudiant"]=dateUS2FR($etudiantRecup->dateIAC2IEtudiant);
				$_SESSION["numRegime"]=$etudiantRecup->numRegime;
				$_SESSION["idEtape"]=$etudiantRecup->idEtape;
				$_SESSION["C2IValide"]=$etudiantRecup->C2IValide;

$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="stylesheet" href="css/bootstrap.css">
		<link rel="stylesheet" href="css/style.css">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>c2i-1 Connexion</title>
	</head>
	<body class="container-fluid">
		<div id="bandeau" class="row">
			<img class="col-md-4 col-md-offset-4" src="images/logo_c2i1.png" alt="c2i-1">
		</div>
		<div id="connexion">
			<br>
			<p>Vous êtes maintenant connecté.<br>Vous allez être redirigé vers votre espace personnel.<br>Si ce n'est pas le cas, <a href="index.php">cliquez-ici</a>
			</p>
			<br>
		</div>
	</body>
</html>
HTML;

				echo $html;

			}
	}

	

	}
}