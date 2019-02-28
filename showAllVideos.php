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

if (isset($_SESSION['user'])) { /*User is logged in */
    $user = new User($_SESSION['user']);

    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();   
    
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

    $video = new Video();
    $res = $video->getAllVideosWithLecturers();
    $content['result'] = $res;

    /* Only show parts of the description */
    if($res){
        foreach ($res as &$video) {
        $new_desc = substr($video['description'], 0, 80) . " [...]";
        $video['description'] = $new_desc;
        }  
        echo $twig->render('showAllVideos.html', $content);
    }  else {
        header("Location: showAllVideos.php?status=feil");
    } 




