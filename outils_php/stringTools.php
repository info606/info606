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

/* Format dd/mm/yyyy vers yyyy-mm-dd */
function dateToMySQL($date)
{
    $temp = explode("/", $date); /* 0=> jour, 1=> mois, 2=> année */
    $jour = sprintf("%02d", $temp[0]);
    $mois = sprintf("%02d", $temp[1]);
    $annee = sprintf("%04d", $temp[2]);

    $res = $annee.'-'.$mois.'-'.$jour;

    return $res;
}

function dateToPassword($date)
{
    $temp = explode("/", $date); /* 0=> jour, 1=> mois, 2=> année */
    $jour = sprintf("%02d", $temp[0]);
    $mois = sprintf("%02d", $temp[1]);
    $annee = sprintf("%04d", $temp[2]);

    $res = $jour.'/'.$mois.'/'.$annee;

    return $res;
}