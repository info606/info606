<?php

session_start();
require_once("/outils_php/autoload.php");
require_once("outils.php");

$menu = getMenuEnseignant();

if(verifConnexion("enseignant")){

	$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
	<head>
		<link type="text/css" rel="stylesheet" href="css/bootstrap.css">
		<link type="text/css" rel="stylesheet" href="css/style.css">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Gestion de la base du c2i-1</title>
		<script type='text/javascript' src='http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha1.js'></script>
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
				$menu
				<div id="page" class="col-md-7 col-md-offset-1">
					<h2 class="h2">Gestion de la base du c2i-1</h2>
					<br><br>
HTML;

if(isset($_GET['type']) && !empty($_GET['type'])){
	if(isset($_GET["action"]) && !empty($_GET["action"])){
		if($_GET["type"]==1 && $_GET["action"] == 1){
			$etudiantControleur = new EtudiantControleur();
			$e = $etudiantControleur->etudiantManager->recupererParNum($_GET['etudiant']);
			$regimeControleur = new RegimeControleur();
			$tabRegime = $regimeControleur->regimeManager->recupererTout();
			$etapeControleur = new EtapeControleur();
			$tabEtape = $etapeControleur->etapeManager->recupererTout();
			$dateNais = dateUS2FR($e->dateNaisEtudiant);
			$dateiae = dateUS2FR($e->dateIAEEtudiant);
			$datec2i = dateUS2FR($e->dateIAC2IEtudiant);

			$html.=<<<HTML
<form action="modifbase.php" method="POST" class="form-horizontal" role="form" onSubmit="this.mdpEtudiant=CryptoJS.SHA1(this.mdpEtudiant)">
	<input type="hidden" name="action" value="modifEtudiant">
	<input type="hidden" name="numEtudiant" value="{$e->numEtudiant}">
	<div class="form-group">
		<label class="col-sm-3 control-label">Nom</label>
		<div class="col-sm-4">
			<input name="nomEtudiant" type="text" class="form-control" id="inputEmail3" placeholder="Nom de l'étudiant" value="{$e->nomEtudiant}">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Prénom</label>
		<div class="col-sm-4">
			<input name="prenomEtudiant" type="text" class="form-control" id="inputEmail3" placeholder="Nom de l'étudiant" value="{$e->prenomEtudiant}">
		</div>
	</div>
	<div class="form-group">
		<label for="inputPassword3" class="col-sm-3 control-label">Mot de passe</label>
		<div class="col-sm-4">
			<input name="mdpEtudiant" type="password" class="form-control" id="inputPassword3" placeholder="Mot de passe" value="{$e->mdpEtudiant}">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Mail</label>
		<div class="col-sm-4">
			<input name="mailEtudiant" type="text" class="form-control" id="inputEmail3" placeholder="E-mail de l'étudiant" value="{$e->mailEtudiant}">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Date de naissance</label>
		<div class="col-sm-4">
			<input name="naisEtudiant" type="text" class="form-control" id="inputEmail3" placeholder="Date de naissance de l'étudiant" value="{$dateNais}">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Login</label>
		<div class="col-sm-4">
			<input name="loginEtudiant" type="text" class="form-control" id="inputEmail3" placeholder="Login de l'étudiant" value="{$e->loginEtudiant}">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Date IAE</label>
		<div class="col-sm-4">
			<input name="dateIAEEtudiant" type="text" class="form-control" id="inputEmail3" placeholder="Date IAE de l'étudiant" value="{$dateiae}">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Date Inscription c2i</label>
		<div class="col-sm-4">
			<input name="dateIAC2IEtudiant" type="text" class="form-control" id="inputEmail3" placeholder="Date IAC2I l'étudiant" value="{$datec2i}">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Régime</label>
		<div class="col-sm-4">
			<select name="regimeEtudiant" class="form-control">
HTML;

			foreach($tabRegime as $t){
				if($t != null){
					if($t->numRegime == $e->numRegime)
						$html.="<option value='".$t->numRegime."' selected>".$t->libRegime."</option>\n";
					else
						$html.="<option value='".$t->numRegime."'>".$t->libRegime."</option>\n";
				}
				
			}

			$html.=<<<HTML
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Etape</label>
		<div class="col-sm-4">
			<select name="etapeEtudiant" class="form-control">
HTML;

			foreach($tabEtape as $t){
				if($t != null){
					if($t->idEtape == $e->idEtape)
						$html.="<option value='".$t->idEtape."' selected>".$t->libLongEtape."</option>\n";
					else
						$html.="<option value='".$t->idEtape."'>".$t->libLongEtape."</option>\n";
				}
				
			}

$html.=<<<HTML
			</select>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-4">
			<button type="submit" class="btn btn-default">Valider les modifications</button>
		</div>
	</div>
</form>
HTML;

		}
		else if($_GET["type"]==1 && $_GET["action"] == 2){
			$etudiant = new Etudiant($_GET["etudiant"]);
			$etudiantControleur = new EtudiantControleur();
			$etudiantControleur->etudiantManager->supprimer($etudiant);

			$html.="<h4 class='h4'>Suppression réussie.</h4>";
		}
		else if($_GET["type"]==1 && $_GET["action"] == 4){
			// Réinitialisation du mot de passe
			$etudiantControleur = new EtudiantControleur();
			$etudiant = $etudiantControleur->etudiantManager->recupererParNum($_GET["etudiant"]);
			$etudiant->mdpEtudiant = sha1(dateUS2FR($etudiant->dateNaisEtudiant));
			$etudiantControleur->etudiantManager->maj($etudiant);

			$html.="<h4 class='h4'>Réinitialisation du mot de passe réussie.</h4>";
		}
		else if($_GET["type"]==2 && $_GET["action"] == 1){
			$enseignantControleur = new EnseignantControleur();
			$e = $enseignantControleur->enseignantManager->recupererParLogin($_GET["enseignant"]);

			$html.=<<<HTML
<form action="modifbase.php" method="POST" class="form-horizontal" role="form" onSubmit="this.mdpEnseignant=CryptoJS.SHA1(this.mdpEnseignant)">
	<input type="hidden" name="action" value="modifEnseignant">
	<input type="hidden" name="numEnseignant" value="{$e->numEnseignant}">
	<div class="form-group">
		<label class="col-sm-3 control-label">Nom</label>
		<div class="col-sm-4">
			<input name="nomEnseignant" type="text" class="form-control" id="inputEmail3" placeholder="Nom de l'enseignant" value="{$e->nomEnseignant}">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Prénom</label>
		<div class="col-sm-4">
			<input name="prenomEnseignant" type="text" class="form-control" id="inputEmail3" placeholder="Prénom de l'enseignant" value="{$e->prenomEnseignant}">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Login</label>
		<div class="col-sm-4">
			<input name="loginEnseignant" type="text" class="form-control" id="inputEmail3" placeholder="Login de l'enseignant" value="{$e->loginEnseignant}">
		</div>
	</div>
	<div class="form-group">
		<label for="inputPassword3" class="col-sm-3 control-label">Mot de passe</label>
		<div class="col-sm-4">
			<input name="mdpEnseignant" type="password" class="form-control" id="inputPassword3" placeholder="Mot de passe" value="{$e->mdpEnseignant}">
		</div>
	</div>
	<div class="form-group">
		<label for="inputComposante3" class="col-sm-3 control-label">Composante</label>
		<div class="col-sm-4">
			<select name="compEnseignant" class="form-control" id="inputcomposante3">
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
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-4">
			<button type="submit" class="btn btn-default">Valider les modifications</button>
		</div>
	</div>
</form>
HTML;
		}
		else if($_GET["type"]==2 && $_GET["action"] == 2){
			$enseignant = new Enseignant("","","",$_GET["enseignant"]);
			$enseignantControleur = new EnseignantControleur();
			$enseignantControleur->enseignantManager->supprimer($enseignant);

			$html.="<h4 class='h4'>Suppression réussie.</h4>";			
		}
		else if($_GET["type"]==1 && $_GET["action"] == 3){
			/* Création d'un étudiant*/
			$regimeControleur = new RegimeControleur();
			$tabRegime = $regimeControleur->regimeManager->recupererTout();
			$etapeControleur = new EtapeControleur();
			$tabEtape = $etapeControleur->etapeManager->recupererTout();

			$html.=<<<HTML
<form action="modifbase.php" method="POST" class="form-horizontal" role="form">
	<input type="hidden" name="action" value="creaEtudiant">
	<div class="form-group">
		<label class="col-sm-3 control-label">Numéro étudiant</label>
		<div class="col-sm-4">
			<input name="numEtudiant" type="text" class="form-control" id="inputEmail3" placeholder="Numéro de l'étudiant">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Nom</label>
		<div class="col-sm-4">
			<input name="nomEtudiant" type="text" class="form-control" id="inputEmail3" placeholder="Nom de l'étudiant">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Prénom</label>
		<div class="col-sm-4">
			<input name="prenomEtudiant" type="text" class="form-control" id="inputEmail3" placeholder="Nom de l'étudiant">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Mail</label>
		<div class="col-sm-4">
			<input name="mailEtudiant" type="text" class="form-control" id="inputEmail3" placeholder="E-mail de l'étudiant">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Date de naissance</label>
		<div class="col-sm-4">
			<input name="naisEtudiant" type="text" class="form-control" id="inputEmail3" placeholder="Date de naissance de l'étudiant"> (format: JJ/MM/AAAA)
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Login</label>
		<div class="col-sm-4">
			<input name="loginEtudiant" type="text" class="form-control" id="inputEmail3" placeholder="Login de l'étudiant">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Date IAE</label>
		<div class="col-sm-4">
			<input name="dateIAEEtudiant" type="text" class="form-control" id="inputEmail3" placeholder="Date IAE de l'étudiant"> (format: JJ/MM/AAAA)
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Date Inscription c2i</label>
		<div class="col-sm-4">
			<input name="dateIAC2IEtudiant" type="text" class="form-control" id="inputEmail3" placeholder="Date IAC2I l'étudiant"> (format: JJ/MM/AAAA)
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Régime</label>
		<div class="col-sm-4">
			<select name="regimeEtudiant" class="form-control">
HTML;

			foreach($tabRegime as $t){
				if($t != null){
					$html.="<option value='".$t->numRegime."'>".$t->libRegime."</option>\n";
				}
				
			}

			$html.=<<<HTML
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Etape</label>
		<div class="col-sm-4">
			<select name="etapeEtudiant" class="form-control">
HTML;

			foreach($tabEtape as $te){
				if($te != null){
					$html.="<option value='".$te->idEtape."'>".$te->libLongEtape."</option>\n";
				}
				
			}

			$html.=<<<HTML
			</select>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-4">
			<button type="submit" class="btn btn-default">Créer l'étudiant</button>
		</div>
	</div>
</form>
HTML;
		}
		else if($_GET["type"]==2 && $_GET["action"] == 3){
			/* Création d'un enseignant*/
			$html.=<<<HTML
<form action="modifbase.php" method="POST" class="form-horizontal" role="form" onSubmit="this.mdpEnseignant=CryptoJS.SHA1(this.mdpEnseignant)">
	<input type="hidden" name="action" value="creaEnseignant">
	<div class="form-group">
		<label class="col-sm-3 control-label">Nom</label>
		<div class="col-sm-4">
			<input name="nomEnseignant" type="text" class="form-control" id="inputEmail3" placeholder="Nom de l'enseignant">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Prénom</label>
		<div class="col-sm-4">
			<input name="prenomEnseignant" type="text" class="form-control" id="inputEmail3" placeholder="Prénom de l'enseignant">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Login</label>
		<div class="col-sm-4">
			<input name="loginEnseignant" type="text" class="form-control" id="inputEmail3" placeholder="Login de l'enseignant">
		</div>
	</div>
	<div class="form-group">
		<label for="inputPassword3" class="col-sm-3 control-label">Mot de passe</label>
		<div class="col-sm-4">
			<input name="mdpEnseignant" type="password" class="form-control" id="inputPassword3" placeholder="Mot de passe">
		</div>
	</div>
	<div class="form-group">
		<label for="inputComposante3" class="col-sm-3 control-label">Composante</label>
		<div class="col-sm-4">
			<select name="compEnseignant" class="form-control" id="inputcomposante3">
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
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-4">
			<button type="submit" class="btn btn-default">Créer l'enseignant</button>
		</div>
	</div>
</form>
HTML;
		}
	}
	else{
		if($_GET["type"]==1){
			$etudiantControleur = new EtudiantControleur();
			$tabEtudiant = $etudiantControleur->etudiantManager->recupererTout();
			$html.="<h3 class='h3'>Gestion des étudiants</h3><br>";
			$html.= "<table id='tableaugestionbase' class='table table-bordered'><tr><td>Numéro<td>Nom<td>Prénom<td>Actions\n";

			foreach($tabEtudiant as $e){
				if($e != null){
					$html.="<tr><td>".$e->numEtudiant."<td>".$e->nomEtudiant."<td>".$e->prenomEtudiant."<td><a href='gestionbase.php?type=1&action=1&etudiant=".$e->numEtudiant."'><button type='button' class='btn btn-info'>Editer</button></a><a href='gestionbase.php?type=1&action=2&etudiant=".$e->numEtudiant."'><button type='button' class='btn btn-danger'>Supprimer</button></a><a href='gestionbase.php?type=1&action=4&etudiant=".$e->numEtudiant."'><button type='button' class='btn btn-warning'>Réinitialiser MDP</button></a>";
				}
			}

			$html.="<tr><td><td><td><td><a href='gestionbase.php?type=1&action=3'><button type='button' class='btn btn-success'>Créer un étudiant</button></a>";

			$html.="</table>\n";
		}
		else if($_GET["type"]==2){
			$enseignantControleur = new EnseignantControleur();
			$tabEnseignant = $enseignantControleur->enseignantManager->recupererTout();
			$html.="<h3 class='h3'>Gestion des enseignants</h3><br>";
			$html.= "<table id='tableaugestionbase' class='table table-bordered'><tr><td>Numéro<td>Nom<td>Prénom<td>Actions\n";

			foreach($tabEnseignant as $e){
				if($e != null){
					$html.="<tr><td>".$e->numEnseignant."<td>".$e->nomEnseignant."<td>".$e->prenomEnseignant."<td><a href='gestionbase.php?type=2&action=1&enseignant=".$e->loginEnseignant."'><button type='button' class='btn btn-info'>Editer</button></a><a href='gestionbase.php?type=2&action=2&enseignant=".$e->loginEnseignant."'><button type='button' class='btn btn-danger'>Supprimer</button></a>";
				}
			}

			$html.="<tr><td><td><td><td><a href='gestionbase.php?type=2&action=3'><button type='button' class='btn btn-success'>Créer un enseignant</button></a>";

			$html.="</table>\n";

		}
		else
			$html.="<h4 class='h4'>Une erreur est survenue.</h4>";
	}
}
	else{
		$html.=<<<HTML
					<h3 class='h3'>Quelle donnée souhaitez-vous gérer?</h3>
					<br>
					<a href="gestionbase.php?type=1">Etudiants</a><br>
					<a href="gestionbase.php?type=2">Enseignants</a><br><br><br>
HTML;
}

$html.=<<<HTML
				</div>
				<br>
			</div>

			<br><br><br>
		</div>

	</body>
</html>
HTML;

echo $html;
	
}
else{
	header('Location: nonautorise.php'); 
}