<?php

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

/* Retourne la date du fichier sous la forme dd/mm/yyyy */
function dateFromFilename($filename)
{
    /* On enlève l'extension */
    $temp = explode(".", $filename);
    /* On récupère la date sous le format ddmmyyyy */
    $temp = explode("_", $temp[0]);
    $date = $temp[3];

    $jour = substr($date, 0,2);
    $mois = substr($date, 2,2);
    $annee = substr($date, 4);

    $date = $jour.'/'.$mois.'/'.$annee;

    return $date;
}

/* Prend en paramètre une date sous la forme dd/mm/yyyy */
function dateFromText($text)
{
    $temp = explode("/", $ext);

    $date['j'] = $temp[0];
    $date['m'] = $temp[1];
    $date['y'] = $temp[2];

    return $date;
}

/* Prend en paramètre une date sous le format dd/mm/yyyy */
/* Retourne l'année scolaire de la date */
function anneeScolaire($date)
{
    $temp = explode("/", $date);

    $jour = $temp[0];
    $mois = $temp[1];
    $annee = $temp[2];

    $moisDeb = 9;

    return ($mois<$moisDeb)?$annee:$annee+1;
}