<?php
/*
Twig renderer for upload.html

For å hente ut informasjon fra formen og sende til upload manager(som sender til DB)
*/
require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/DB.php';
require_once 'classes/video.php';

$content = array();

session_start();

if (isset($_SESSION['user'])) { //User is logged in
    $user = new User($_SESSION['user']);

    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();    
}

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

    $user = new User($_SESSION['user']);
    $uid = $user->returnId();

    $video = new Video();
    $res = $video->getAllUserVideos($uid);

    if($res) {
        $content['result'] = $res;
       echo $twig->render('showUserVideos.html', $content);
    } else {
       echo("Ingen videoer å vise.");
    } 




