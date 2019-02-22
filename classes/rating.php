<?php
require_once 'db.php';

class Rating
{

    public function addRating($uid, $videoid, $rating){

        $db = new DB();
        
        $res = $db->newRating($uid, $videoid, $rating);
        
        if ($res) {
            echo "Database Success!";
            return true;
        } else {
             echo "Failed to insert rating to database!";
            return false;
            }
        
    }
        
    public function getAllRatings($videoid) {
            $db = new DB();
        
            $res = $db->returnAllRatings($videoid);
        
            if($res) {
                return $res;
            }else {
                //print_r("failed getting ratings!");
            }
           
    }
        
    public function getTotalRatings($videoid){
        $db = new DB();
        
            $res = $db->returnTotalRatings($videoid);
        
            if($res) {
                return $res;
            }else {
                //print_r("failed getting ratings!");
            }

    }

}


?>