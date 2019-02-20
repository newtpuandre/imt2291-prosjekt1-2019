<?php

require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/video.php';
require_once 'classes/comment.php';


$content = array();

session_start();

if (isset($_SESSION['user'])) { //User is logged in

    $user = new User($_SESSION['user']);
    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();
    
    
}

$videoid = -1;

if (isset($_GET['id'])) {
    $video = new Video();
    $videoid = $_GET['id'];   
    $content['videoinfo'] = $video->getVideo($videoid);
}

if(isset($_POST['submit_btn'])) {
    $video = new Video();
    $videoid = $_POST['video_id'];   
    $content['videoinfo'] = $video->getVideo($videoid);

    $comment_txt = $_POST['comment_text'];

    $comment = new Comment();
    $uid = $content['userinfo'];
    $res = $comment->addComment($uid, $videoid, $comment_txt);

}

$commentinfo = new Comment();
$content['comments'] = $commentinfo->getAllComments($videoid);
$content['video_id'] = $videoid;

/* If id was not set in GET or POST */
if ($videoid == -1) { //Need a video id inorder for the site to have any purpose..
    header("Location: index.php");
}

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

echo $twig->render('playVideo.html', $content);
