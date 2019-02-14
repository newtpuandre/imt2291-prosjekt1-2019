<?php
//REMEMBER TO CHECK IF USER CAN EDIT SELECTED VIDEO

require_once 'vendor/autoload.php';
require_once 'classes/user.php';

$content = array();

session_start();

if (isset($_SESSION['user'])) { //User is logged in

    $user = new User($_SESSION['user']);

    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();

    if ($content['userprivileges'] < 1) { //Redirect if user isnt authorized
        header("Location: index.php");
    }
}

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

echo $twig->render('editVideo.html', $content);
