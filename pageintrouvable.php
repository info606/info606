<?php

$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
	<head>
		<link type="text/css" rel="stylesheet" href="css/bootstrap.css">
		<link type="text/css" rel="stylesheet" href="css/style.css">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Page introuvable</title>
	</head>
	<body class="container-fluid">
		<br>
		<div id="bandeau" class="row">
			<img class="col-md-4 col-md-offset-4" src="images/logo_c2i1.png" alt="c2i-1">	
		</div>
		<br><br>
		<div id="nonautorise">
			<br><br>
			<h2 class="h2">La page que vous recherchez est introuvable.<br>
			<small><a href="index.php">Cliquez-ici pour revenir à l'accueil</a></small></h2>
		</div>
	</body>
</html>
HTML;

echo $html;