<?php

date_default_timezone_set("Africa/Casablanca");


//router ( backend and frontend application )
const DEFAULT_APP = 'Frontend';
$app = DEFAULT_APP;
$requestURI = $_SERVER['REQUEST_URI'];
$requestArray = explode('/', $requestURI);
$requestArray = array_filter($requestArray);
$requestArray = array_values($requestArray);

if (strtolower($requestArray[0]) == 'backend') $app = 'Backend';



if (isset($_GET['app']) && $_GET['app'] == 'BuildEntity') {

    require 'tableAttributes/BuildEntity.php';
    $o = new BuildEntity('HistoriqueService');
    exit;
}
// elseif (isset($_GET['app']) && $_GET['app'] == 'managerMaker') {
//     require 'managerMaker.php';
//     $o = new managerMaker('Agent');
//     exit;
// }

if (isset($_GET['app']) && $_GET['app'] == 'test') {
    require 'test/test.php';
    exit;
}


// // On commence par inclure la classe nous permettant d'enregistrer nos autoload
require __DIR__ . '/../App/Backend/Lib/SplClassLoader.php';

// // Si l'application n'est pas valide, on va charger l'application par défaut qui se chargera de générer une erreur 404
// if (!isset($_GET['app']) || !file_exists(__DIR__ . '/../App/' . $_GET['app'])) $_GET['app'] = DEFAULT_APP;
if ($app == 'Backend') {

    header("Content-type: JSON");

    // // On va ensuite enregistrer les autoloads correspondant à chaque vendor (OCFram, App, Model, etc.)
    $libLoader = new SplClassLoader('Lib', __DIR__ . '/../App/Backend');
    $libLoader->register();

    $entityLoader = new SplClassLoader('Entity', __DIR__ . '/../App/Backend');
    $entityLoader->register();

    $modelLoader = new SplClassLoader('Model', __DIR__ . '/../App/Backend');
    $modelLoader->register();

    $modelLoader = new SplClassLoader('View', __DIR__ . '/../App/Backend');
    $modelLoader->register();

    $modelLoader = new SplClassLoader('Controller', __DIR__ . '/../App/Backend');
    $modelLoader->register();

    require __DIR__ . '/../App/Backend/Vendor/autoload.php';
    require __DIR__ . '/../App/Backend/Vendor/functionsLib/functions.php';
}


$libLoader = new SplClassLoader('App', __DIR__ . '/..');
$libLoader->register();


// // Il ne nous suffit plus qu'à déduire le nom de la classe et de l'instancier
$appClass = 'App\\' . $app . '\\' . $app . 'Application';



$app = new $appClass;

$app->run();