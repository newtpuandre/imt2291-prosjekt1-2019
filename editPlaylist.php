<?php
//REMEMBER TO CHECK IF USER CAN EDIT SELECTED PLAYLIST

require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/db.php'; //Note to self. Remove and use video / playlist class instead

$content = array();

session_start();

/*
MODE Variable:
$content['mode'] is not set : Default overview of playlists
$content['mode'] = 1; : Create new playlist
$content['mode'] = 2; : Edit current playlist
$content['mode'] = 3; : Add videos to playlist
*/

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

}  elseif ((isset($_GET['update']) && isset($_GET['addVideos'])) || isset($_POST['addVideos'])) { //Add videos to playlist

    if(isset($_POST['addVideos'])) {

        //Add videos

       header("Location: editPlaylist.php?update=".$_POST['playlistId']);
    }

    $content['mode'] = 3;

    //Get information about current playlist
    $content['playlistItem'] = $db->returnPlaylist($_GET['update'], $content['userid']);


    //Load videos and present them
    //$content['videos'] = $db->returnVideos($content['userid'], 0, 20); //Only show 20 at a time 
    $content['videos'] = $db->returnVideos($content['userid']);



}  elseif (isset($_GET['update']) || isset($_POST['update'])) { //Update selected playlist
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
