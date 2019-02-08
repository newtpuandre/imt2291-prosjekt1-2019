<?php
/*
Twig renderer for login.html
*/
require_once 'vendor/autoload.php';

//This needs to be replaced once we actualy send data to the renderer..
$emptyArray = array();

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

echo $twig->render('login.html', $emptyArray);
