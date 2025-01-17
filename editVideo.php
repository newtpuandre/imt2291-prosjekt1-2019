<?php
//REMEMBER TO CHECK IF USER CAN EDIT SELECTED VIDEO

require_once 'vendor/autoload.php';
require_once 'classes/user.php';
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

$videoid = -1;

if (isset($_GET['status'])) { /*Get the status.*/
    $content['status'] = $_GET['status'];
    $content['status_message'] = "Oppdateringsfeil. ";
}

if (isset($_POST['button_edit'])) {
    $video = new Video();
    $videoid = $_POST['video_id'];   
    $content['videoinfo'] = $video->getVideo($videoid);
}

if (isset($_POST['button_update'])) {
    $video = new Video();
    $videoid = $_POST['video_id']; 
    $title = $_POST['update_title'];
    $description = $_POST['update_desc'];
    $topic = $_POST['update_topic'];
    $course = $_POST['update_course'];

    $content['videoinfo'] = $video->getVideo($videoid);
  

    if(!isset($_FILES['update_thumbnail'])){
        $thumbnail = $content['videoinfo'][0]['thumbnail_path'];
    } else {
        $thumbnail = $_FILES['update_thumbnail'];
    }
    
    
    $res = $video->updateVideo($videoid, $title, $description, $topic, $course, $thumbnail);
    if(!$res){
        header("Location: editVideo.php?status=feil");  
    }

    header("Location: showUserVideos.php"); 
}

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', /* Only enable cache when everything works correctly */
));

echo $twig->render('editVideo.html', $content);


