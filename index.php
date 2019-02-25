<?php
/*
Twig renderer for index.html
*/
require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/video.php';
require_once 'classes/playlist.php';

$content = array();

session_start();

$video = new Video();
$playlist = new Playlist();

if (isset($_SESSION['user'])) { //User is logged in

    $user = new User($_SESSION['user']);

    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();
    $content['userid'] = $user->returnId();
    
    $content['subscribedPlaylists'] = $playlist->getSubscribedPlaylists($content['userid']);
}



$content['newVideos'] = $video->getNewVideos();


$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

echo $twig->render('index.html', $content);
