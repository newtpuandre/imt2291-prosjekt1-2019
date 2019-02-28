<?php
require_once 'vendor/autoload.php';
require_once 'classes/user.php';
require_once 'classes/playlist.php';
require_once 'classes/admin.php';

$content = array();

session_start();

if (isset($_SESSION['user'])) { /*User is logged in*/

    $user = new User($_SESSION['user']);

    $content['userinfo'] = $user->returnEmail();
    $content['userprivileges'] = $user->getPrivileges();
    $content['userid'] = $user->returnId();
    
    if ($content['userprivileges'] == "2") { /*Check for admin privileges*/
        $admin = new Admin();
        $return = $admin->countIAmTeacher();
        $content['isTeacherCount'] = $return['num'];
    }
}

/*
MODE Variable:
$content['mode'] is not set : Default overview of playlists
$content['mode'] = 1; : Show videos in the playlists
$content['mode'] = 2; : Search for playlists
*/

$playlist = new Playlist();

if (isset($_GET['show'])) { /* Show playlist with specific id */

    $content['mode'] = 1;

    $content['currentPlaylist'] = $playlist->resolveVideos($_GET['show']); /* Resolve videos in playlist */ 
    $content['playlistInfo'] = $playlist->returnPlaylist($_GET['show']);   /* Get information about current playlist */

    /*Get subscription number*/
    $content['subscriberNum'] = $playlist->countSubscribers($_GET['show']);

    if(isset($content['userinfo'])){ /* Is user logged in? */
        /*Get subscription status*/
        $content['subscribed'] = $playlist->returnSubscriptionStatus($_GET['show'], $content['userid']);
    }


    if(isset($_GET['subscribe'])) { /* Subscribe user */

        $res = $playlist->subscribeToPlaylist($_GET['show'], $content['userid']);

        if ($res) { /* Was it succesful? */
            header("Location: playlist.php?show=".$_GET['show']);
        } else{
            header("Location: playlist.php?show=".$_GET['show']."&status=feil");
        }


    } elseif (isset($_GET['unsubscribe'])) { /* Unsubscribe user */

        $res = $playlist->unsubscribeToPlaylist($_GET['show'], $content['userid']);

        if ($res) {/* Was it succesful? */
            header("Location: playlist.php?show=".$_GET['show']);
        } else {
            header("Location: playlist.php?show=".$_GET['show']."&status=feil");
        }

    }

}  else { /* Show all playlists */
    if (isset($_GET['search'])) { /* Search for a playlist */
        $content['mode'] = "2";
        $content['search'] = $_GET['search'];

        $res = $playlist->searchForPlaylists($_GET['search']);

        if ($res) {/* Was it succesful? */
            $content['playlists'] = $res;
        } else {
            $content['status'] = "feil";
        }

    } else { /* Return all playlists*/
        $res = $playlist->returnAllPlaylists();
        if ($res) {/* Was it succesful? */
            $content['playlists'] = $res;
        } else {
            $content['status'] = "feil";
        }
    }

}

if (isset($_GET['status'])) { /* Pass status to user */
    $content['status'] = $_GET['status'];
}



$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache', // Only enable cache when everything works correctly 
));

echo $twig->render('playlist.html', $content);
