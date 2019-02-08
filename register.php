<?php
/*
Twig renderer for register.html
*/

require_once 'vendor/autoload.php';
require_once 'classes/db.php';

if (isset($_POST['createNewUser'])) { // Create new user

    //Initalize a new database connection
    $db = new DB();
    //Get an instance of the current connection
    $dbh = $db->getDBConnection();

    $sql = 'INSERT INTO users (username, email , password) values (?, ?, ?)';
    $sth = $dbh->prepare ($sql);
    // Use password_hash to encrypt password : http://php.net/manual/en/function.password-hash.php
    $sth->execute (array ($_POST['username'], $_POST['email'],
                          password_hash($_POST['password'], PASSWORD_DEFAULT)));
    if ($sth->rowCount()==1) {
        header('Location: register.php?status=ok');
    } else {
        header('Location: register.php?status=feil');
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

echo $twig->render('register.html', $statusArray);
