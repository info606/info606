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
				<li><a href="uploadForm.php">Importer des données</a></li>
				<li><a href="supres.php">Supprimer des résultats</a></li><br>
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

////////////////////////////////////////////////////////////////////////////////
    /// Production d'un code aléatoire (minuscule, majuscule et chiffre)
function codeAleatoire($taille /** Taille de la chaîne aléatoire */) {
    $c = '' ;
    for ($i=0; $i<$taille; $i++) {
        switch (rand(0, 2)) {
            case 0 :
                $c .= chr(rand(ord('A'), ord('Z'))) ;
                break ;
            case 1 :
                $c .= chr(rand(ord('a'), ord('z'))) ;
                break ;
            case 2 :
                $c .= chr(rand(ord('1'), ord('9'))) ;
                break ;
        }
    }
    return $c ;
}
?>