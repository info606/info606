<?php

require_once("/outils_php/autoload.php");

$csv = new CSVLoader("excel/etapes.csv",";");

print_r($csv->getHeadLine());

print_r($csv->getIndexTitle(array("etape", "code")));