<?php

require_once('stringTools.php');

function emptyFilter($var)
{
    return ($var==0 || !empty($var));
}

function emptyArrayFilter($var)
{
    return (count($var) != 0);
}


function arrayDateSort( $a, $b)
{
	$nameA = explode("_", $a);
	$nameB = explode("_", $b);

	$dateA = $nameA[2];
	$dateB = $nameB[2];

	/* On transforme en timestamp */
	$dateA = strtotime(substr($dateA,4,4).'-'.substr($dateA,2,2).'-'.substr($dateA, 0,2));
	$dateB = strtotime(substr($dateB,4,4).'-'.substr($dateB,2,2).'-'.substr($dateB, 0,2));

	if($dateA < $dateB)
		return -1;
	
	if($dateA > $dateB)
		return 1;

	return 0;

}

function arrayToUTF8($array)
{
	foreach ($array as $key=>$value) {
		$array[$key] = utf8_encode($value);
	}
	return $array;
}

function arrayToNoAccent(&$array)
{
	foreach ($array as $key=>$value) {
		$array[$key] = minusculesSansAccents($value);
	}
}