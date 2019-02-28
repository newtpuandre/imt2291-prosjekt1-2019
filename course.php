<?php

require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/video.php';
require_once 'classes/comment.php';
require_once 'classes/rating.php';
require_once 'classes/admin.php';


$content = array();

session_start();
if (isset($_GET['status'])) { /*Get the status.*/
    $content['status'] = $_GET['status'];
    $content['status_message'] = "Finner ingen videoer.";
}

if (isset($_SESSION['user'])) { /*User is logged in*/

    $user = new User($_SESSION['user']);
    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();
    $content['uid'] = $user->returnId();
    
    if ($content['userprivileges'] == "2") { /*Check for admin privileges*/
        $admin = new Admin();
        $return = $admin->countIAmTeacher();
        $content['isTeacherCount'] = $return['num'];
    }

    $video = new Video();
}

if (isset($_POST['button_search'])){ /* Search for a course */

    $prompt = $_POST['search_prompt'];

    $res = $content['result'] = $video->searchVideoCourse($prompt);


} else {
    $res = $content['result'] = $video->getAllVideoCourses();
}

if(!$res && !isset($_GET['status'])){
    header("Location: course.php?status=feil");
}


$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', /* Only enable cache when everything works correctly */
));

echo $twig->render('course.html', $content);
