<?php

session_start();
require_once("/outils_php/autoload.php");
require_once("outils.php");

$menu = getMenuEtudiant();

if(verifConnexion("etudiant")){
	/*$validationControleur = new ValidationControleur();
	$tabValidation = $validationControleur->validationManager->recupererParNumEtudiant($_SESSION["numEtudiant"]);
	$epreuveControleur = new EpreuveControleur();
	$tabEpreuve = $epreuveControleur->epreuveManager->recupererTout();
*/
	$validationControleur = new ValidationControleur();
	$etapeControleur = new EtapeControleur();
	$etape = $etapeControleur->etapeManager->recupererParNum($_SESSION["idEtape"]);
	$epreuveControleur = new EpreuveControleur();
	$tabEpreuve = $epreuveControleur->epreuveManager->recupererParNumComposante($etape->numComposante);
	$tabValidation = $validationControleur->validationManager->recupererParNumEtudiant($_SESSION["numEtudiant"]);

	$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
	<head>
		<link type="text/css" rel="stylesheet" href="css/bootstrap.css">
		<link type="text/css" rel="stylesheet" href="css/style.css">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Mes résultats</title>
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
					<h2 class="h2">Mes résultats</h2>
					<br><br>
					<table id="tableauValidation" class="table table-bordered">
HTML;

	$annee = 0;
	$tab = array();
	//$tab[0] = array("<td>Année</td>");
	$tab[0] = array("Année");

	$k=1;
	for($i=1;$i<count($tabValidation);$i++){
		$anneeCoup = substr($tabValidation[$i]->dateValidation,0,4);
		if($anneeCoup != $annee){
			$annee = $anneeCoup;
			//$tab[$k]=array("<td>".$anneeCoup."</td>");
			$tab[$k]=array($anneeCoup);
			$k++;
		}
	}

	for($i=1;$i<count($tabEpreuve);$i++){
		//$tab[0][$i]="<td>".$tabEpreuve[$i]->libEpreuve."</td>";
		$tab[0][$i]=$tabEpreuve[$i]->libEpreuve;
	}

	/*$html.=count($tab)."-".count($tab[0]);*/
	for($i=1;$i<count($tab);$i++){
		for($j=1;$j<count($tab[0]);$j++){
			//$tab[$i][$j]="<td class='danger'>Non validée</td>";
			$tab[$i][$j]="Non validée";
		}
	}

	for($k=1;$k<count($tabValidation);$k++){
		$anneeCoup = substr($tabValidation[$k]->dateValidation,0,4);
		$numEp = $tabValidation[$k]->idEpreuve;
		$nomEp = ".";
		for($i=1;$i<count($tabEpreuve);$i++){
			if($tabEpreuve[$i]->idEpreuve == $numEp)
				$nomEp = $tabEpreuve[$i]->libEpreuve;
		}

		for($i=1;$i<count($tab);$i++){
			if($tab[$i][0]==$anneeCoup){
				for($j=1;$j<count($tab[0]);$j++){
					if($tab[0][$j]==$nomEp){
						$date = substr($tabValidation[$k]->dateValidation,8,2)."-".substr($tabValidation[$k]->dateValidation,5,2)."-".$anneeCoup;
						//$tab[$i][$j]="<td class='success'>Validée le ".$date."</td>";
						$tab[$i][$j]="Validée le ".$date;
					}
				}
			}
		}
	}

	$date = date("Y");

	$nbrLigne = count($tab);
	$tab[$nbrLigne][0]="Sur 3 ans";
	for($i=1;$i<count($tab[0]);$i++){
		$col =0;
		for($j=1;$j<$nbrLigne;$j++){
			if(substr($tab[$j][$i],0,1) == 'V' && ($date-3)<= $tab[$j][0])
				$col=1;
		}

		if($col==1)
			$tab[$nbrLigne][$i]="Validée";
		else
			$tab[$nbrLigne][$i]="Non validée";
	}

	for($i=0;$i<count($tab);$i++){
		if($i == count($tab)-1){
			$html.="<tr><td>";
			for($j=1;$j<count($tab[0]);$j++)
				$html.="<td class='active'>";
			$html.="</tr>";
		}
		$html .= "<tr>";
		for($j=0;$j<count($tab[0]);$j++){
			if(substr($tab[$i][$j],0,1) == 'N'){
				$html.="<td class='danger'>".$tab[$i][$j]."</td>";
			}
			else if(substr($tab[$i][$j],0,1) == 'V'){
				$html.="<td class='success'>".$tab[$i][$j]."</td>";
			}
			else{
				$html.="<td>".$tab[$i][$j]."</td>";
			}
		}
		$html.="</tr>\n";
	}


	$html .= <<<HTML

					</table>
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
