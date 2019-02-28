<?php

/**
  *  Twig renderer for upload.html
  *
  * For uploading a video to the database
  */


require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/DB.php';
require_once 'classes/video.php';
require_once 'classes/admin.php';

$content = array();

session_start();

/* If the user is logged in */
if (isset($_SESSION['user'])) { 
    $user = new User($_SESSION['user']);

    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();   
    
    /*Redirect if the user isn't authorized*/
    if ($content['userprivileges'] < 1) { 
        header("Location: index.php");
    }

    if ($content['userprivileges'] == "2") { //Check for admin privileges
        $admin = new Admin();
        $return = $admin->countIAmTeacher();
        $content['isTeacherCount'] = $return['num'];
    }
}

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

if (isset($_GET['status'])) { /*Get the status.*/
    $content['status'] = $_GET['status'];
}

/* If the upload video button isn't pressed */
if (!isset($_FILES['upload_video'])){
    echo $twig->render('upload.html', $content);
    exit();
}

if(isset($_POST['upload_btn'])) {
    $title = $_POST['upload_title'];
    $description = $_POST['upload_desc'];
    $topic = $_POST['upload_topic'];
    $course = $_POST['upload_course'];
    $videofile = $_FILES['upload_video'];
    $thumbnail = $_FILES['upload_thumbnail'];
    
    $uid = $user->returnId();
    
    $video = new Video();
    $res = $video->upload($uid, $title, $description, $topic, $course, $videofile, $thumbnail);

   

    if($res) {
        echo $twig->render('index.html', $content);
    } else {
        header("Location: videoUpload.php?status=feil");
    } 

}

?>