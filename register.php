<?php
/*
Twig renderer for register.html
*/

require_once 'vendor/autoload.php';
require_once 'classes/db.php';

if (isset($_POST['createNewUser'])) { /* Create new user*/

    /*Initalize a new database connection*/
    $db = new DB();

    $registerStatus = $db->registerUser($_POST['name'],$_POST['email'],$_POST['password'],$_POST['isTeacher']);
    
    if ($registerStatus) { /*Everything went well*/
        header('Location: register.php?status=ok');
    } else { /*Something went wrong*/
        header('Location: register.php?status=feil');
    }

}


$statusArray = array();

if (isset($_GET['status'])) { /*Pass status array to user.*/
    $statusArray['code'] = $_GET['status'];
}

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', /* Only enable cache when everything works correctly */
));

echo $twig->render('register.html', $statusArray);
