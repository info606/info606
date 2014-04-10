<?php

session_start();
require_once("/outils_php/autoload.php");
require_once("outils.php");

$menu = getMenuEnseignant();

if(verifConnexion("enseignant")){

	$etudiantControleur = new EtudiantControleur();
	$tabEtudiant = $etudiantControleur->etudiantManager->recupererTout();

	$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
	<head>
		<link type="text/css" rel="stylesheet" href="css/bootstrap.css">
		<link type="text/css" rel="stylesheet" href="css/style.css">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Gestion du c2i-1</title>
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
					<h2 class="h2">Gestion du c2i-1</h2>
					<br><br>
					<h3 class="h3">Choix de l'étudiant</h3>
					<form action="gestionc2i.php" method="POST">
						<div class="col-md-4">
							<select name="etudiant" class="form-control">
								<option value="none"></option>
HTML;

foreach($tabEtudiant as $e){
	if($e != NULL)
		$html .= "<option value='".$e->numEtudiant."'>".$e->nomEtudiant." ".$e->prenomEtudiant."</option>\n";
}

$html .= <<<HTML
							</select>
						</div>
						 <button type="submit" class="btn btn-default">Rechercher</button>
					</form>
					<br><br>
HTML;

$afficher = false;
$validationControleur = new ValidationControleur();

if(isset($_GET["epreuve"]) && isset($_GET["etudiant"]) && isset($_GET["date"])){
	$epreuve = $_GET["epreuve"];
	$date = $_GET["date"];
	$etudiant = $_GET["etudiant"];
	$enseignant = $_SESSION["numEnseignant"];

	$validation = new Validation($date,$enseignant,$etudiant,$epreuve);
	$validationControleur->validationManager->ajouter($validation);

	$html .= "<h4 class='h4'>Validation enregistrée.</h4>";
	$afficher = true;
}

if((isset($_POST["etudiant"]) && !empty($_POST["etudiant"]) && $_POST["etudiant"]!="none") || $afficher){

	if($afficher)
		$_POST["etudiant"]=$_GET["etudiant"];

	$etudiant = $etudiantControleur->etudiantManager->recupererParNum($_POST["etudiant"]);
	$etapeControleur = new EtapeControleur();
	$etape = $etapeControleur->etapeManager->recupererParNum($etudiant->codeEtape);
	$epreuveControleur = new EpreuveControleur();
	$tabEpreuve = $epreuveControleur->epreuveManager->recupererParNumComposante($etape->numComposante);
	
	$tabValidation = $validationControleur->validationManager->recupererParNumEtudiant($etudiant->numEtudiant);

	$date = date("Y");

	$html .= "<table id='tableauEtudiantValidation' class='table table-bordered'>"/*\n<tr><td>Année"*/;

	$i=1;
	$tabEp = array();
	$tabEp[0]=array("");
	$tabEp[1]=array("<tr><td>");
	$tabEp[2]=array("<tr><td>Sur 3 ans");
	$tabEp[3]=array("<tr><td>Année en cours");
	foreach($tabEpreuve as $t){
		if($t != NULL){
			//$html.="<td>".$t->libEpreuve."</td>";
			$tabEp[0][$i]=$t->idEpreuve;
			$tabEp[1][$i]="<td>".$t->libEpreuve;
			$i++;
		}
	}

	for($i=1;$i<count($tabEp[0]);$i++){
		$tabEp[2][$i]="<td class='danger'>Non validée";
		//$tabEp[3][$i]="N";
		$tabEp[3][$i]="<td><a href='gestionc2i.php?etudiant=".$etudiant->numEtudiant."&epreuve=".$tabEp[0][$i]."&date=".date("Y-m-d")."'><button type='button' class='btn btn-success'>Valider</button></a></td>";
	}

	/*$html .= "\n<tr class='active'><td>Sur 3 ans";*/
	foreach($tabValidation as $v){
		$oldDate="0";
		if($v!=NULL){
			if(substr($v->dateValidation,0,4)>=($date-3)){
				$dateCoup = substr($v->dateValidation,0,4);
				if($dateCoup > $oldDate){
					$oldDate = substr($v->dateValidation,0,4);

					for($i=1;$i<count($tabEp[0]);$i++){
						if($tabEp[0][$i]==$v->idEpreuve)
							$tabEp[2][$i]="<td class='success'>Validée le ".substr($v->dateValidation,8,2)."/".substr($v->dateValidation,5,2)."/".substr($v->dateValidation,0,4);
					}

				}

			}
		}
	}

	foreach($tabValidation as $v){
		if($v != null){
			$dateCoup = substr($v->dateValidation,0,4);

			for($i=1;$i<count($tabEp[0]);$i++){
				if($tabEp[0][$i]==$v->idEpreuve){
					if($dateCoup == $date){
						$tabEp[3][$i]="<td class='success'>Déjà validée</td>";
					}
					/*else{
						$tabEp[3][$i]="<td><a href='gestionc2i.php?etudiant=".$etudiant->numEtudiant."&epreuve=".$tabEp[0][$i]."&enseignant=".$_SESSION["numEnseignant"]."&date=".date("Y-m-d")."'><button type='button' class='btn btn-success'>Valider</button></a></td>";
					}*/
				}
				else{
					if($tabEp[3][$i] == "N"){
						$tabEp[3][$i]="<td><a href='gestionc2i.php?etudiant=".$etudiant->numEtudiant."&epreuve=".$tabEp[0][$i]."&date=".date("Y-m-d")."'><button type='button' class='btn btn-success'>Valider</button></a></td>";
					}
				}
			}
		}
	}

	for($i=1;$i<count($tabEp);$i++){
		for($j=0;$j<count($tabEp[0]);$j++){
			$html.=$tabEp[$i][$j];
		}
	}

	$html .="</table>";
}
else if(isset($_POST["etudiant"]) && !empty($_POST["etudiant"]) && $_POST["etudiant"]=="none"){
	$html.="<h4 class='h4'>Vous devez selectionner un étudiant!</h4>";
}

$html .= <<<HTML
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
	header('Location: nonautorise.php'); 
}