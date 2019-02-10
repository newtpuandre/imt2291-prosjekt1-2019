<?php
/*
Twig renderer for login.html
*/
require_once 'vendor/autoload.php';
require_once "classes/db.php";

if (isset($_POST['login'])) {  // Log in user
    //Initalize a new database connection
    $db = new DB();

    //Get an instance of the current connection
    $dbh = $db->getDBConnection();

    $sql = 'SELECT password, id FROM users WHERE username=:username OR email=:username';
    $sth = $dbh->prepare ($sql);
    $sth->bindParam(':username', $_POST['username']);
    $sth->execute();
    if ($row = $sth->fetch()) { // get id and hashed password for given user
        // Use password_verify to check given password : http://php.net/manual/en/function.password-verify.php
        if (password_verify($_POST['password'], $row['password'])) {
            //Login was successful! Start session below.
            session_start();

            //All information we need for other sites need to be saved into the session variable.
            $_SESSION["user"] = $_POST['username'];

            header('Location: index.php');

            //$loginStatus = "Bruker logget inn med brukerid={$row['id']}";
        } else {
            header('Location: login.php?status=feil');
            //$loginStatus = 'Feil passord';
        }
    } else {
        //$loginStatus = 'Ingen bruker med det brukernavnet';
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
