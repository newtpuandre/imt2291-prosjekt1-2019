<?php

require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/video.php';
require_once 'classes/comment.php';
require_once 'classes/rating.php';


$content = array();

session_start();

if (isset($_SESSION['user'])) { //User is logged in

    $user = new User($_SESSION['user']);
    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();
    $content['uid'] = $user->returnId();
    
    
}

if (isset($_POST['button_search'])){

    $video = new Video();
    
    $prompt = $_POST['search_prompt'];

    $content['result'] = $video->searchVideoCourse($prompt);
    //DO STUFF

} else {

    $video = new Video();
    $content['result'] = $video->getAllVideoCourses();


}


$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

echo $twig->render('course.html', $content);