<?php
require_once 'vendor/autoload.php';
require_once 'classes/user.php';

$content = array();

session_start();

if (isset($_SESSION['user'])) { //User is logged in && is admin

    $user = new User($_SESSION['user']);

    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();

}

$content['search'] = $_GET['search'];

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

echo $twig->render('search.html', $content);