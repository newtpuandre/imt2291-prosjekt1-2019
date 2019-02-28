<?php
require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/video.php';
require_once 'classes/admin.php';

$content = array();

session_start();

if (isset($_SESSION['user'])) { //User is logged in && is admin

    $user = new User($_SESSION['user']);

    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();

    if ($content['userprivileges'] == "2") { //Check for admin privileges
        $admin = new Admin();
        $return = $admin->countIAmTeacher();
        $content['isTeacherCount'] = $return['num'];
    }

}
if (isset($_GET['status'])) { /*Get the status.*/
    $content['status'] = $_GET['status'];
    $content['status_message'] = "Finner ingen videoer. ";
}


$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

if(isset($_GET['search'])){ /* Handle search */
    $content['search'] = $_GET['search']; /* Search input*/

    $prompt = $content['search']; /* Set variable prompt for further use*/

    $video = new Video();
    $res = $content['result'] = $video->search($prompt); /* Querry database for search */

    if(!$res){ /* If error */
        header("Location: search.php?status=feil");  
    }
} 

echo $twig->render('search.html', $content);