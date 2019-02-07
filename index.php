<?php
/*
Twig renderer for index.html
*/
require_once 'vendor/autoload.php';

$emptyArray = array();

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

echo $twig->render('index.html', $emptyArray); //Replace NULL with data you want to pass
