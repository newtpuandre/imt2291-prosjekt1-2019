<?php

require_once 'db.php';

class Video
{
    static private $target_dir = "uploads/";

    function __construct() { }

    public function upload($uid, $title, $description, $topic, $course, $video, $thumbnail) {
        $video_path = Video::$target_dir . basename($video["name"]);
        $thumb_path = Video::$target_dir . basename($thumbnail["name"]);

        $video_file_type = strtolower(pathinfo($video_path, PATHINFO_EXTENSION));
        $thumb_file_type = strtolower(pathinfo($thumb_path, PATHINFO_EXTENSION));

        /* TODO : Return meaningful error for all of these, for now, debug echos */
        if (file_exists($video_path) || file_exists($thumb_path)) {
            echo "FILE EXISTS!!!";
            return false;
        }

        if ($thumb_file_type != "jpg" && $thumb_file_type != "png" && $thumb_file_type != "jpeg" && $thumb_file_type != "gif") {
            echo "Thumb format must be jpg, png, jpeg or gif";
            return false;
        }

        if ($video_file_type != "mp4") {
            echo "Video must be mp4";
            return false;
        }

        if(move_uploaded_file($video["tmp_name"], $video_path) && move_uploaded_file($thumbnail["tmp_name"], $thumb_path)) {
            echo "The files uploaded successfully!";

            /* Inser info in database */
            $db = new DB();
            $res = $db->newVideo($uid, $title, $description, $topic, $course, $thumb_path, $video_path);

            if ($res) {
                echo "Database Success!";
                return true;
            } else {
                echo "Failed to insert to database!";

                /* Make sure we delete uploaded files if database could not be updated! */
                unlink($video_path);
                unlink($thumb_path);
                return false;
            }
        } else {
            echo "Woops, couldn't upload.";
            return false;
        }        
    }   
}

?>