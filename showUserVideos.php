<?php
/*
Twig renderer for upload.html

For å hente ut informasjon fra formen og sende til upload manager(som sender til DB)
*/
require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/DB.php';
require_once 'classes/video.php';
require_once 'classes/admin.php';

$content = array();

session_start();

if (isset($_SESSION['user'])) { /*User is logged in*/
    $user = new User($_SESSION['user']);

    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();  
    
    if ($content['userprivileges'] < 1) { /*Redirect if user isnt authorized*/
        header("Location: index.php");
    }

    if ($content['userprivileges'] == "2") { /*Check for admin privileges*/
        $admin = new Admin();
        $return = $admin->countIAmTeacher();
        $content['isTeacherCount'] = $return['num'];
    }
}

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', /* Only enable cache when everything works correctly */
));

if (isset($_GET['status'])) { /*Get the status.*/
    $content['status'] = $_GET['status'];
    $content['status_message'] = "Finner ingen videoer. ";
} 

$user = new User($_SESSION['user']);
$uid = $user->returnId();

$video = new Video();
$res = $video->getAllUserVideos($uid); /* Get this $uid's videos*/

if($res){

    if (isset($_POST['button_edit'])){ /* If the edit video button is pressed, redirect */
        echo $twig->render('editVideo.html', $content);
    }
 
    foreach ($res as &$video) {
        $new_desc = substr($video['description'], 0, 50) . " [...]";
        $video['description'] = $new_desc;
    }  
    $content['result'] = $res;
} elseif (!isset($_GET['status'])){
    header("Location: showUserVideos.php?status=feil");
}

echo $twig->render('showUserVideos.html', $content);