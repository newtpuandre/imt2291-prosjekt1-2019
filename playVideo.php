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

$videoid = -1;

if (isset($_GET['id'])) {
    $video = new Video();
    $videoid = $_GET['id'];   
    $content['videoinfo'] = $video->getVideo($videoid);
    $content['lecturer'] = $video->getVideoLecturer($videoid);
    $content['lecturer'] = $content['lecturer'][0]['name'];
}

if(isset($_POST['submit_btn'])) {
    $video = new Video();
    $videoid = $_POST['video_id'];   
    $content['videoinfo'] = $video->getVideo($videoid);

    $comment_txt = $_POST['comment_text'];

    $comment = new Comment();
    $uid = $content['uid'];
    $comment->addComment($uid, $videoid, $comment_txt);
}

if(isset($_POST['submit_rating'])) {
    $video = new Video();
    $videoid = $_POST['video_id'];   
    $content['videoinfo'] = $video->getVideo($videoid);

    if ($content['userprivileges'] > 0) { /* Only students can rate videos */
        unset($_POST);
        header("Location: playVideo.php?id=".$videoid);
    }

    if(isset($_POST['rating'])){
        $video_rating = $_POST['rating'];
    } else {
        $video_rating = 0;
    }
    $rating = new Rating();
    $uid = $content['uid'];
   
    $prev = $rating->getRating($uid, $videoid);

    if($prev != null){
        $rating->updateRating($uid, $videoid, $video_rating);
    } else {
        $rating->addRating($uid, $videoid, $video_rating);
    }
    
}

if(isset($_POST['button_delete'])){
    $video = new Video();
    $videoid = $_POST['video_id'];   
    $content['videoinfo'] = $video->getVideo($videoid);

    $comment = new Comment();
    $commentid = $_POST['comment_id'];   

    print_r($commentid);
    $comment->deleteComment($commentid);
}


    $commentinfo = new Comment();
    $content['comments'] = $commentinfo->getAllComments($videoid);
    $content['video_id'] = $videoid;


    $ratinginfo = new Rating();
    $allRatings = $ratinginfo->getAllRatings($videoid);

    if($allRatings > 0){
    $tmpRatings = $ratinginfo->getTotalRatings($videoid);
    $ratings = 0;
    $totalRatings = $tmpRatings[0][0];


    
   foreach($allRatings as $value){
        $ratings += $value[0];
    }

    $avgRating = $ratings/(float)$totalRatings; //TODO: CALCULATE THIS
    $avgRating = number_format((float)$avgRating, 2, '.', '');
    $content['avg_rating'] = $avgRating;
    $content['total_rating'] = $totalRatings;

    } else {
        $content['avg_rating'] = 0;
        $content['total_rating'] = 0;
    }


/* If id was not set in GET or POST */
if ($videoid == -1) { //Need a video id inorder for the site to have any purpose..
    header("Location: index.php");
}

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

echo $twig->render('playVideo.html', $content);
