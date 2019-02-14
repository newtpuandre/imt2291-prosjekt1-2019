<?php
//REMEMBER TO CHECK IF USER CAN EDIT SELECTED PLAYLIST

require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/db.php';

$content = array();

session_start();

if (isset($_SESSION['user'])) { //User is logged in

    $user = new User($_SESSION['user']);

    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();
    $content['userid'] = $user->returnId();

    if ($content['userprivileges'] < 1) { //Redirect if user isnt authorized
        header("Location: index.php");
    }

    $db = new DB();
}

if (isset($_GET['createNew']) || isset($_POST['createNew'])){ //Create new playlist
    $content['mode'] = 1;

    if (isset($_POST['createNew'])){

        //Insert new playlist into the db
        if ($db->insertPlaylist($content['userid'], $_POST['name'], $_POST['description'])) {
            header("Location: editPlaylist.php");
        } else {
            //error
        }

    }

} elseif (isset($_GET['update']) || isset($_POST['update'])) { //Update selected playlist
    $content['mode'] = 2;

    $content['playlistItem'] = $db->returnPlaylist($_GET['update'], $content['userid']);

    if(isset($_POST['update'])) {

        //Update playlist
        $db->updatePlaylist($_POST['id'],$content['userid'],$_POST['name'],$_POST['description']);

        header("Location: editPlaylist.php?update=".$_POST['id']); //Refresh page to see changes
    }

} else { //No params sent

    //Load playlists
    $content['playlists'] = $db->returnPlaylists($content['userid']);

}

//Handle button delete logic..

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

echo $twig->render('editPlaylist.html', $content);
