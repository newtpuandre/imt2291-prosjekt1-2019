<?php
/*
Twig renderer for upload.html

For å hente ut informasjon fra formen og sende til upload manager(som sender til DB)
*/
require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/DB.php';
require_once 'classes/video.php';

$content = array();

session_start();

if (isset($_SESSION['user'])) { //User is logged in
    $user = new User($_SESSION['user']);

    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();  
    $content['uid'] = $user->returnId();
    $content['name'] = $user->returnName();
    $content['password'] = $user->returnPassword();
    $content['picture'] = $user->returnPicture();
}

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

if (isset($_POST['button_update'])) {
    
    $name = $_POST['update_name']; 
    $username = $_POST['update_username'];
    $password = $_POST['update_password'];
    $profilepic = $_POST['update_picture'];
   
    $content['videoinfo'] = $video->getVideo($videoid);
  

    if(!isset($_FILES['update_thumbnail'])){
        $thumbnail = $content['videoinfo'][0]['thumbnail_path'];
    } else {
        $thumbnail = $_FILES['update_thumbnail'];
    }
    
    
    $user->updateUser($videoid, $title, $description, $topic, $course, $thumbnail);

    header("Location: profile.php"); 
}





echo $twig->render('profile.html', $content);


?>