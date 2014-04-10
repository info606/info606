<?php

$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once($rootPath."/info606/outils_php/autoload.php");
require_once($rootPath."/info606/classes/excelloader.php");
ini_set('memory_limit', '200M'); /* Augmentation du cache pour le tableau */
$el = new ExcelLoader("etapes.xlsx");

$data = $el->getDonnees();

$composanteC = new ComposanteControleur();
$cursusC = new CursusControleur();
$etapeC = new EtapeControleur();

//print_r($data);

/* Pour chaque ligne on récupère les données suivantes */
foreach ($data as $ligne) {
	print_r($ligne);
	/* Composante */
	$composante = new Composante();
	/* 	Code de la composante */
	$composante->codeComposante = $ligne["Composante (code)"];
	/* 	Libellé de la composante */
	$composante->libComposante = $ligne["Composante (lib.)"];

	$composanteC->composanteManager->ajouter($composante);

	/* Cursus */
	$cursus = new Cursus();
	/*	Code du cursus */
	$cursus->codeCursus = $ligne["Etape - Cursus LMD (code)"];
	/*	Lib du cursus */
	$cursus->libCursus = $ligne["Etape - Cursus LMD (lib.)"];
	/*	Niveau du cursus */
	$cursus->niveau = $ligne["Niveau dans le diplôme"];

	$cursusC->cursusManager->ajouter($cursus);

	/* Etape */
	$etape = new Etape();
	/*	Code de l'étape */
	$etape->codeEtape = $ligne["Etape (code)"];
	/*	Code du cursus */
	$etape->idCursus = $cursusC->cursusManager->recupererNum($cursus);
	/*	Numéro de la composante */
	$etape->numComposante = $composanteC->composanteManager->recupererNum($composante);
	/* Libélé court de l'étape */
	$etape->libCourtEtape = $ligne["Etape (lib.)"];
	/* Version de l'étape */
	$etape->versionEtape = $ligne["Version d'étape (code)"];
	/* Version de l'étape */
	$etape->libLongEtape = $ligne["Version d'étape (lib. web)"];

	$etapeC->etapeManager->ajouter($etape);
}
