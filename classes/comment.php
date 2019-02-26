<?php
require_once 'db.php';

class Comment
{

 public function addComment($uid, $videoid, $comment){

    $db = new DB();

    $res = $db->newComment($uid, $videoid, $comment);

    if ($res) {
        echo "Database Success!";
        return true;
    }else {
        echo "Failed to insert to database!";
        return false;
    }

 }

 public function getAllComments($videoid) {
    $db = new DB();

    $res = $db->returnAllComments($videoid);

    if($res) {
        return $res;
    }else {
        //print_r("failed getting comments!");
    }

}

public function deleteComment($commentid){

    $db = new DB();

        
    $res = $db->deleteComment($commentid);

    if($res) {
        return true;
    }else {
        print_r("Failed deleting comment!");
    }
}


   
}



?>