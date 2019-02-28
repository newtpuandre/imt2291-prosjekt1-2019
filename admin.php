<?php
/*
Twig renderer for index.html
*/
require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/admin.php';

$content = array();

session_start();

//Gather user list
$admin = new Admin();
$content['userlist'] = $admin->gatherUsers();

if (isset($_SESSION['user'])) { /*User is logged in && is admin*/

    $user = new User($_SESSION['user']);

    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();

    if ($content['userprivileges'] != 2) { /*Redirect if user isnt authorized*/
        header("Location: index.php");
    } else {
        $admin = new Admin();
        $return = $admin->countIAmTeacher();
        $content['isTeacherCount'] = $return['num']; 
    }

}

if (isset($_POST['updateUser'])) { /*Update privileges of the selected user*/
    //Check if this went OK
    $res = $admin->updatePrivileges($_POST['email'],$_POST['privileges']);
    if(!$res){
        header("Location: admin.php?status=feil");
    }
    header("Location: admin.php"); /*Refresh the page inorder for the list to update*/

}

if (isset($_GET['remove'])) {
    $res = $admin->removeIAmTeacher($_GET['remove']);
    if(!$res){
        header("Location: admin.php?status=feil");
    }
    header("Location: admin.php"); /*Refresh the page inorder for the list to update*/
}

if (isset($_GET['status'])) { /*Something went wrong.*/
    $content['status'] = "feil";
}
//


$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', /* Only enable cache when everything works correctly */
));

echo $twig->render('admin.html', $content);