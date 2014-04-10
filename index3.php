<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];

//require_once($rootPath."/info606/outils_php/autoload.php");
require_once($rootPath."/info606/excelloader.php");

$el = new ExcelLoader("C2i-1_2013_IA-A.xlsx");

print_r($el->getDonnees());

