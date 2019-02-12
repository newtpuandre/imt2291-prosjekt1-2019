<?php
/*
Twig renderer for login.html
*/
require_once 'vendor/autoload.php';
require_once "classes/db.php";

if (isset($_POST['login'])) {  // Log in user
    //Initalize a new database connection
    $db = new DB();

    $loginStatus = $db->loginUser($_POST['email'],$_POST['password']);

    if ($loginStatus) { //Everything went well

        //Login was successful! Start session below.
        session_start();
    
        //All information we need for other sites need to be saved into the session variable.
         $_SESSION["user"] = $_POST['email'];
    
        header('Location: index.php');

    } else { //Something went wrong

        header('Location: login.php?status=feil');
        
    }

}

$statusArray = array();

if (isset($_GET['status'])) { //Pass status array to user.
    $statusArray['code'] = $_GET['status'];
}

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

echo $twig->render('login.html', $statusArray);
