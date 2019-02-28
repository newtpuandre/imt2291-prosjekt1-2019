<?php

require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/playlist.php';
require_once 'classes/admin.php';

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

    if ($content['userprivileges'] == "2") { //Check for admin privileges
        $admin = new Admin();
        $return = $admin->countIAmTeacher();
        $content['isTeacherCount'] = $return['num'];
    }

    $playlist = new Playlist();
}

if (isset($_GET['status'])) { //Get the status.
    $content['status'] = $_GET['status'];
}

if (isset($_GET['createNew']) || isset($_POST['createNew'])){ //Create new playlist
    $content['mode'] = 1;

    if (isset($_POST['createNew'])){
        //Insert new playlist into the db
        $res = $playlist->insertPlaylist($content['userid'], $_POST['name'], $_POST['description'], $_FILES['thumbnail_file']);
        if ($res) {
            header("Location: editPlaylist.php");
        } else {
            header("Location: editplaylist.php?status=feil");
        }

    }

}  elseif (isset($_GET['addVideos']) || isset($_POST['addVideos'])) { //Add videos to playlist

    if(isset($_POST['addVideos'])) {

        //Add videos
        foreach ($_POST['videoids'] as &$value) {

            //Add videos to playlist
            $res = $playlist->addVideoToPlaylist($_POST['playlistId'], $value);
        }
        
        if ($res) {
            header("Location: editPlaylist.php?update=".$_POST['playlistId']);
        } else {
            header("Location: editplaylist.php?addVideos&status=feil");
        }

    }

    $content['mode'] = 3;

    //Get information about current playlist
    $content['playlistItem'] = $playlist->returnPlaylist($_GET['update']);


    //Load videos and present them
    //$content['videos'] = $db->returnVideos($content['userid'], 0, 20); //Only show 20 at a time 
    $content['videos'] = $playlist->returnVideos($content['userid']);

}  elseif (isset($_GET['update']) || isset($_POST['update'])) { //Update selected playlist
    $content['mode'] = 2;

    if(isset($_POST['update'])) {

        //Update playlist
        $res = $playlist->updatePlaylist($_POST['id'],$content['userid'],$_POST['name'],$_POST['description'], $_FILES['thumbnail_file']);

        if ($res) {
            header("Location: editPlaylist.php?update=".$_POST['id']); //Refresh page to see changes
        } else {
            header("Location: editPlaylist.php?update=".$_POST['id']."&status=feil");
        }
    }

    if(isset($_GET['down'])){

        if ($_GET['down'] == "true") {
            $res = $playlist->editPosition($_GET['update'], $_GET['id'], true);
        } else { //false
            $res = $playlist->editPosition($_GET['update'], $_GET['id'], false);
        }

        if ($res) {
            header("Location: editPlaylist.php?update=".$_GET['update']); //Refresh page to see changes
        } else {
            header("Location: editPlaylist.php?update=".$_GET['update']."&status=feil");
        }

    }

    if(isset($_GET['delete'])) {
        
        //Delete video from playlist
        $res = $playlist->deleteVideoFromPlaylist($_GET['update'], $_GET['delete']);
        
        if ($res) {
            header("Location: editPlaylist.php?update=".$_GET['update']);
        } else {
            header("Location: editPlaylist.php?update=".$_GET['update']."&status=feil");
        }
    
    }

    $content['playlistItem'] = $playlist->returnPlaylist($_GET['update']);

    $idarray = $playlist->returnPlaylistVideos($_GET['update']);

    $temp = $playlist->resolveVideos($_GET['update']);

    if ($temp) {
        $content['playlistVideos'] = $temp;
    }

} elseif (isset($_GET['deletePlaylist'])) {

    $res = $playlist->deletePlaylist($_GET['deletePlaylist'], $content['userid']);

    if ($res) {
        header("Location: editPlaylist.php");   
    } else {
        header("Location: editPlaylist.php?status=feil");
    }

} else { //No params sent

    //Load playlists
    $content['playlists'] = $playlist->returnPlaylists($content['userid']);
}

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

echo $twig->render('editPlaylist.html', $content);
