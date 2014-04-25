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

if((isset($_POST["etudiant"]) && !empty($_POST["etudiant"]) && $_POST["etudiant"]!="none")){

	$html .= "<table id='tableauEtudiantValidation' class='table table-bordered'>";

	$etudiant = $etudiantControleur->etudiantManager->recupererParNum($_POST["etudiant"]);
	$validationControleur = new ValidationControleur();
	$etapeControleur = new EtapeControleur();
	$etape = $etapeControleur->etapeManager->recupererParNum($etudiant->idEtape);
	$epreuveControleur = new EpreuveControleur();
	$tabEpreuve = $epreuveControleur->epreuveManager->recupererParNumComposante($etape->numComposante);
	$tabValidation = $validationControleur->validationManager->recupererParNumEtudiantTrieParDate($etudiant->numEtudiant);

	$annee = 0;
	$tab = array();
	$tab[0] = array("Epreuve");
	$tab[1] = array("Année");

	$k=2;
	for($i=1;$i<count($tabValidation);$i++){
		$anneeCoup = substr($tabValidation[$i]->dateValidation,0,4);
		if($anneeCoup != $annee){
			$annee = $anneeCoup;
			$tab[$k]=array($anneeCoup);
			$k++;
		}
	}

	for($i=1;$i<count($tabEpreuve);$i++){
		$tab[0][$i]=$tabEpreuve[$i]->idEpreuve;
		$tab[1][$i]=$tabEpreuve[$i]->libEpreuve;
	}

	// Initialisation par défaut à NV
	for($i=2;$i<count($tab);$i++){
		for($j=1;$j<count($tab[0]);$j++){
			$tab[$i][$j]="N.V";
		}
	}

	// Remplissage du tableau
	foreach($tabValidation as $t){
		if($t != NULL){
			$date = $t->dateValidation;
			$dateDecoup = substr($date,0,4);
			$epreuve = $t->idEpreuve;
			$val = $t->valeurValidation;

			$continu = 1;
			for($i=2;$i<count($tab) && $continu==1;$i++){
				if($tab[$i][0] == $dateDecoup)
					$continu = 0;
			}

			$continu=1;
			for($j=1;$j<count($tab[0]) && $continu==1;$j++){
				if($tab[0][$j] == $epreuve)
					$continu = 0;
			}

			if($val == -1){
				$tab[$i-1][$j-1] = "E.C";
			}
			else if($val == 0){
				$tab[$i-1][$j-1] = "N.V";
			}
			else
				$tab[$i-1][$j-1]=dateUS2Fr($date);
		}
	}

	// Ajout de la ligne final sur 3 ans
	$date = date("Y");
	$nbrLigne = count($tab);
	$tab[$nbrLigne][0]="Sur 3 ans";
	for($i=1;$i<count($tab[0]);$i++){
		$col =0;
		for($j=2;$j<$nbrLigne;$j++){
			if(substr($tab[$j][$i],0,1) != 'E' && substr($tab[$j][$i],0,1) != 'N' && ($date-3)<= $tab[$j][0])
				$col=1;
		}

		if($col==1)
			$tab[$nbrLigne][$i]="Validée";
		else
			$tab[$nbrLigne][$i]="Non validée";
	}


	// Affichage final avec colorisation
	for($i=1;$i<count($tab);$i++){
		// S'il s'agit de l'avant dernière ligne, on saute une ligne
		if($i == count($tab)-1){
			$html.="<tr><td>";
			for($j=1;$j<count($tab[0]);$j++)
				$html.="<td class='active'>";
			$html.="</tr>";
		}

		$html.="<tr>";

		for($j=0;$j<count($tab[0]);$j++){
			if($i>1 && $j>0){
				if(substr($tab[$i][$j],0,1) == "E")
					$html.="<td class='info'>".$tab[$i][$j];
				else if(substr($tab[$i][$j],0,1) == "N")
					$html.="<td class='danger'>".$tab[$i][$j];
				else
					$html.="<td class='success'>".$tab[$i][$j];
			}
			else{
					$html.="<td>".$tab[$i][$j];
			}
		}
			
	}

	$html .="</table>";

$html .= <<<HTML
		<br>	
			<dl class="dl-horizontal"><strong>Légende :</strong>
				<dt>N.V</dt>
			  	<dd>Non Validé (épreuve passée mais échouée).</dd>
			  	<dt>E.C</dt>
			  	<dd>En Cours pour l'année universitaire.</dd>
			  	<dt>Date</dt>
			  	<dd>Validée</dd>
			</dl>
HTML;
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