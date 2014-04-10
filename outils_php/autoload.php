<?php



function __autoload($class_name) {
	$rootPath = $_SERVER['DOCUMENT_ROOT'];
	
	$contr = $rootPath."/info606/controleurs/";
	$manag = $rootPath."/info606/managers/";
	$classe = $rootPath."/info606/classes/";

	// Va chercher le cotrolleur correspondant
	if (is_file($url = $contr . strtolower($class_name) . '.php'))
	{
		require_once $url;
	}

	// Va chercher le manager correspondant
	if (is_file($url = $manag . strtolower($class_name) . '.php'))
	{
		require_once $url;
	}
	
	// Va chercher la classe correspondante
	if (is_file($url = $classe . strtolower($class_name) . '.php'))
	{
		require_once $url;
	}

}
