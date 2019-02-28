<?php

require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/video.php';
require_once 'classes/comment.php';
require_once 'classes/rating.php';
require_once 'classes/admin.php';


$content = array();

session_start();

if (isset($_SESSION['user'])) { //User is logged in

    $user = new User($_SESSION['user']);
    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();
    $content['uid'] = $user->returnId();
    
    if ($content['userprivileges'] == "2") { //Check for admin privileges
        $admin = new Admin();
        $return = $admin->countIAmTeacher();
        $content['isTeacherCount'] = $return['num'];
    }
}

if (isset($_GET['course'])){

    $video = new Video();
    
    $content['course'] = $_GET['course'];

    $content['result'] = $video->searchVideoCourse($content['course'] );

} else {
    print_r("Ingen tema tilgjengelige.");
}


$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

echo $twig->render('topics.html', $content);
