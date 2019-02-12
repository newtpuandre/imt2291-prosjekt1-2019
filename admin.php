<?php
/*
Twig renderer for index.html
*/
require_once 'vendor/autoload.php';

$content = array();

session_start();

if (isset($_SESSION['user'])) { //User is logged in && is admin

    $content['userinfo'] = $_SESSION['user'];


}

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

echo $twig->render('index.html', $content);