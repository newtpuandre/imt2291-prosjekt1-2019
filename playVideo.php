<?php

require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/video.php';
require_once 'classes/comment.php';
require_once 'classes/rating.php';
require_once 'classes/admin.php';


$content = array();

session_start();

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
}

$videoid = -1;

if (isset($_GET['status'])) { /*Get the status.*/
    $content['status'] = $_GET['status'];
}

if (isset($_GET['id'])) { /* Get videoid */
    $video = new Video();
    $videoid = $_GET['id'];   
    $res = $content['videoinfo'] = $video->getVideo($videoid);

    /* If there is no video to get, error */
    if(!$res){
        header("Location: playVideo.php?status=feil");
    }

    $content['lecturer'] = $video->getVideoLecturer($videoid);
    $content['lecturer'] = $content['lecturer'][0]['name'];
}

/* If comment is submitted */ 
if(isset($_POST['submit_btn'])) {
    $video = new Video();
    $videoid = $_POST['video_id'];   
    $res = $content['videoinfo'] = $video->getVideo($videoid);

    if(!$res){
        header("Location: playVideo.php?status=feil");
    }

    $comment_txt = $_POST['comment_text'];

    $comment = new Comment();
    $uid = $content['uid'];
    /* Add comment to DB */
    $comment->addComment($uid, $videoid, $comment_txt);
}

/* If rating is submitted */
if(isset($_POST['submit_rating'])) {
    $video = new Video();
    $videoid = $_POST['video_id'];   
    $res = $content['videoinfo'] = $video->getVideo($videoid);

    if(!$res){
        header("Location: playVideo.php?status=feil");
    }

    if ($content['userprivileges'] > 0) { /* Only students can rate videos */
        unset($_POST);
        header("Location: playVideo.php?id=".$videoid);
    }

    /* If there is a star rating from 1-5 */
    if(isset($_POST['rating'])){
        $video_rating = $_POST['rating'];
    } else {
        /* No rating inserted, which means 0 */
        $video_rating = 0;
    }
    $rating = new Rating();
    $uid = $content['uid'];
   
    /* Check if user already submitted a rating */
    $prev = $rating->getRating($uid, $videoid);

    /* Update rating if there exists one from before */
    if($prev != null){
        $rating->updateRating($uid, $videoid, $video_rating);
    } else {
        /* Add new rating if no previous exists */
        $rating->addRating($uid, $videoid, $video_rating);
    }
    
}
/* If delete button is pressed */
if(isset($_POST['button_delete'])){
    $video = new Video();
    $videoid = $_POST['video_id'];   
    $content['videoinfo'] = $video->getVideo($videoid);

    $comment = new Comment();
    $commentid = $_POST['comment_id'];   

    /* Delete comment */
    $comment->deleteComment($commentid);
}

    /* Get all comments for the video */
    $commentinfo = new Comment();
    $content['comments'] = $commentinfo->getAllComments($videoid);
    $content['video_id'] = $videoid;

    /* Get all ratings for the video */
    $ratinginfo = new Rating();
    $allRatings = $ratinginfo->getAllRatings($videoid);

    /* If there is one or more rating */
    if($allRatings > 0){
    $tmpRatings = $ratinginfo->getTotalRatings($videoid);
    $ratings = 0;
    $totalRatings = $tmpRatings[0][0];

    /* Calculate average rating for the video */
   foreach($allRatings as $value){
        $ratings += $value[0];
    }

    $avgRating = $ratings/(float)$totalRatings;
    $avgRating = number_format((float)$avgRating, 2, '.', '');
    $content['avg_rating'] = $avgRating;
    $content['total_rating'] = $totalRatings;

    } else {
        /* Set the ratings to 0 if there is none */
        $content['avg_rating'] = 0;
        $content['total_rating'] = 0;
    }



$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

echo $twig->render('playVideo.html', $content);
