<?php

function timeDiff($firstTime, $lastTime)
{
    $timeDiff = $lastTime - $firstTime;

    return $timeDiff;
}


$dsn = "mysql:host=localhost;dbname=test";
$options = [
    \PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions for php 8 and more, it's set by default
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, //make the default fetch be an associative array
];

$db = new \PDO($dsn, "root", "", $options);

//-------------------------add------------------------

$id = 1;
$nom = 'ayoub';
$prenom = 'manie';
$values = "('$nom', '$prenom')";


while (true) {

    $values .= ",('$nom', '$prenom')";
    $id++;
    if ($id == 5) break;
}

echo $id;
$requete = "INSERT INTO testTable (nom,prenom)  VALUES $values";

// exit;
$requete = $db->prepare($requete);
$start = time();
$requete->execute();

$end = time();
echo '<div>';
echo timeDiff($start, $end);




//------------------------------update------------------------


// $id = 1;
// $ids = '';
// $nomValues = '';
// $prenomValues = '';



// while (true) {

//     $nomValues .= "when id = $id then '555555' ";
//     $prenomValues .= "when id = $id then 'Cccccc' ";
//     $ids .= "$id,";
//     $id++;
//     if ($id == 1000) break;
// }
// $ids = substr_replace($ids, "", -1);


// echo $id;
// $requete = "UPDATE testTable SET 
//                     nom = (case 
//                         $nomValues
//                     end),
//                     prenom = (case 
//                         $prenomValues
//                     end)
//                     WHERE id in ($ids)";
// // exit;
// // exit($requete);
// $requete = $db->prepare($requete);
// $start = time();
// $requete->execute();

// $end = time();
// echo '<div>';
// echo timeDiff($start, $end);

//-------------------------update independent------------------------

// $id = 1;
// $nom = '555555';
// $prenom = 'Cccccc';
// $values = "('$nom', '$prenom')";


// while (true) {

//     $values .= ",('$nom', '$prenom')";
//     $requetes[] = "UPDATE testTable SET nom = '$nom', prenom = '$prenom' WHERE id= $id ";
//     $id++;

//     if ($id == 1000) break;
// }

// echo $id;


// // exit;
// $start = time();

// foreach ($requetes as $requete) {
//     $requete = $db->prepare($requete);
//     $requete->execute();
// }

// $end = time();
// echo '<div>';
// echo timeDiff($start, $end);