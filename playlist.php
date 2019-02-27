<?php
require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/playlist.php';

$content = array();

session_start();

if (isset($_SESSION['user'])) { //User is logged in

    $user = new User($_SESSION['user']);

    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();
    $content['userid'] = $user->returnId();
    
}

/*
MODE Variable:
$content['mode'] is not set : Default overview of playlists
$content['mode'] = 1; : Show videos in the playlists
*/

$playlist = new Playlist();

if (isset($_GET['show'])) {

    $content['mode'] = 1;

    $content['currentPlaylist'] = $playlist->resolveVideos($_GET['show']);
    $content['playlistInfo'] = $playlist->returnPlaylist($_GET['show']);

    //Get subscription number
    $content['subscriberNum'] = $playlist->countSubscribers($_GET['show']);

    //Get subscription status
    $content['subscribed'] = $playlist->returnSubscriptionStatus($_GET['show'], $content['userid']);


    if(isset($_GET['subscribe'])) {

        $res = $playlist->subscribeToPlaylist($_GET['show'], $content['userid']);

        print_r($res);
        if ($res) {
            header("Location: playlist.php?show=".$_GET['show']);
        } else{
            header("Location: playlist.php?show=".$_GET['show']."&status=feil");
        }


    } elseif (isset($_GET['unsubscribe'])) { 

        $res = $playlist->unsubscribeToPlaylist($_GET['show'], $content['userid']);

        print_r($res);
        if ($res) {
            header("Location: playlist.php?show=".$_GET['show']);
        } else {
            header("Location: playlist.php?show=".$_GET['show']."&status=feil");
        }

    }

}  else {
    $res = $playlist->returnAllPlaylists();
    if ($res) {
        $content['playlists'] = $res;
    } else {
        $content['status'] = "feil";
    }
    //$content['playlists'] = $playlist->returnAllPlaylists();
}

if (isset($_GET['status'])) {
    $content['status'] = $_GET['status'];
}



$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

echo $twig->render('playlist.html', $content);
