<?php

function getMenuEtudiant(){
	$html = <<<HTML
		<div id="menu" class="col-md-2 col-md-offset-1">
			<ul><h2 class='h2'>Menu</h2><br>
				<li><a href="index.php">Accueil</a></li>
				<li><a href="moncompte.php">Mon compte</a></li>
				<li><a href="resultats.php">Voir mes résultats</a></li><br>
			</ul>
		</div>
HTML;

	return $html;
}

function getMenuEnseignant(){
	$html = <<<HTML
		<div id="menu" class="col-md-2 col-md-offset-1">
			<ul><h2 class='h2'>Menu</h2><br>
				<li><a href="index.php">Accueil</a></li>
				<li><a href="moncompte.php">Mon compte</a></li>
				<li><a href="gestionc2i.php">Gestion du c2i-1</a></li>
				<li><a href="gestionbase.php">Gestion de la base</a></li>
				<li><a href="uploadForm.php">Importer des données</a></li><br>
			</ul>
		</div>
HTML;

	return $html;
}

function verifConnexion($typePageAutorisee){
	if(isset($_SESSION) && isset($_SESSION['typePersonne']) && $_SESSION["typePersonne"] == "etudiant"){
		if(isset($_SESSION["numEtudiant"]) && !empty($_SESSION["numEtudiant"]) && ($typePageAutorisee=="etudiant"||$typePageAutorisee=="all"))
			return true;
		else
			header('Location: nonautorise.php');
	}
	else if(isset($_SESSION) && isset($_SESSION['typePersonne']) && $_SESSION["typePersonne"] == "enseignant"){
		if(isset($_SESSION["numEnseignant"]) && !empty($_SESSION["numEnseignant"]) && ($typePageAutorisee=="enseignant"||$typePageAutorisee=="all"))
			return true;
		else
			header('Location: nonautorise.php');
	}
	else
		return false;
}

function dateFR2US($date)
{
  $date = explode('/', $date);
  $date = array_reverse($date);
  $date = implode('-', $date);
  return $date;
}

function dateUS2FR($date)
{
  $date = explode('-', $date);
  $date = array_reverse($date);
  $date = implode('/', $date);
  return $date;
}

?>