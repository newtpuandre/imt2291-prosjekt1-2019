<?php

require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/video.php';


$content = array();

session_start();

if (isset($_SESSION['user'])) { //User is logged in

    $user = new User($_SESSION['user']);
    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();
    
    
}


   

if (isset($_GET['id'])) {
    $video = new Video();
    $videoid = $_GET['id'];
   
   $content['videoinfo'] = $video->getVideo($videoid);
    
} else { //Need a video id inorder for the site to have any purpose..
    header("Location: index.php");
}

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

echo $twig->render('playVideo.html', $content);
