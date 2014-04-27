<?php

function minusculesSansAccents($texte)
{
    $texte = mb_strtolower($texte, 'UTF-8');
    $texte = str_replace(
        array(
            'à', 'â', 'ä', 'á', 'ã', 'å',
            'î', 'ï', 'ì', 'í', 
            'ô', 'ö', 'ò', 'ó', 'õ', 'ø', 
            'ù', 'û', 'ü', 'ú', 
            'é', 'è', 'ê', 'ë', 
            'ç', 'ÿ', 'ñ', 
        ),
        array(
            'a', 'a', 'a', 'a', 'a', 'a', 
            'i', 'i', 'i', 'i', 
            'o', 'o', 'o', 'o', 'o', 'o', 
            'u', 'u', 'u', 'u', 
            'e', 'e', 'e', 'e', 
            'c', 'y', 'n', 
        ),
        $texte
    );
 
    return $texte;        
}

function loginFromFilename($filename)
{
    $temp = explode("_", $filename);

    return $temp[2];
}

function composanteFromFilename($filename)
{
    $temp = explode("_", $filename);

    return $temp[1];
}

function yearFromFilename($filename)
{
    $temp = explode("_", $filename);

    return $temp[0];
}