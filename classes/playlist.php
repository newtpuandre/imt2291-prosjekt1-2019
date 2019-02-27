<?php
require_once 'db.php';

class Playlist
{
    static private $target_dir = "uploads/";

    private $db = null;

    function __construct() {
        //Initalize a new database connection
        $this->db = new DB();
    }

    function __destruct() {
    }

    public function resolveVideos($m_playlistid){

        $idarray = $this->db->returnPlaylistVideos($m_playlistid);
        
        if (!empty($idarray)) {
            foreach ($idarray as &$value) {
                $temp = $this->db->returnVideo($value['videoid']);
                $return[] = $temp[0]; //Get subarray of the temp array
            }

            return $return;

        } else {
            return false;
        }

    }

    public function addVideoToPlaylist($m_playlistid, $m_videoid){

        //Check if video is already in playlist
        if (!$this->db->returnPlaylistVideo($m_playlistid, $m_videoid)){

            $temp = $this->db->returnNewestPlaylistVideo($m_playlistid);

            $lastPos = $temp['position'] + 1;
            
            $res = $this->db->addVideoToPlaylist($m_playlistid, $m_videoid, $lastPos);

            return $res;

        } else {
            return false;
        }

    }

    public function searchForPlaylists($m_search) {
        return $this->db->searchForPlaylists($m_search);
    }

    public function deletePlaylist($m_playlistid, $m_ownerid) {

        return $this->db->deletePlaylist($m_playlistid, $m_ownerid);

    }

    public function deleteVideoFromPlaylist($m_playlistid, $m_videoid) {


        $pos = $this->db->returnPlaylistVideo($m_playlistid, $m_videoid);
        $temp = $this->db->returnPlaylistVideos($m_playlistid);

        $res = $this->db->deleteVideoFromPlaylist($m_playlistid, $m_videoid);

        //find and edit all videoes with pos > removed && do pos-1 as long as pos > 1

        if(count($temp) == 1) { //Last element in list. Do nothing
            return false;
        }

        $i = -1;
        foreach($temp as $value) { //Find index of first element which pos is higher than deleted
            $i++;
            if ($value['position'] > $pos['position']) {
                break;
            } 
        }

         if ($i == -1) { //Did not find a element larger than deleted. Something went wrong.
             return false;
         }

        for ($j = $i; $j < count($temp); $j++) {
            $this->db->editPositionPlaylistVideo($m_playlistid, $temp[$j]['videoid'], $temp[$j]['position']-1); //Move selected item down one

        }

        return $res;

    }

    public function editPosition($m_playlistid,$m_videoid, $m_down){
        $temp = $this->db->returnPlaylistVideos($m_playlistid);
        $editedItem = $this->db->returnPlaylistVideo($m_playlistid, $m_videoid);

        if (count($temp) == 1) { //Do nothing if there are less than 2 items.
            return false;
        }

        if ($m_down) { //Move the pos down to the bottom (highest number)
            
            $i = -1;
            foreach($temp as $value) { //Find index of edited item in all of videos
                $i++;
                if ($value === $editedItem) {
                    break;
                } 
            }

            if ($i == -1) { //$i is only -1 if a match was not found. This should never happen!
                return false;
            }

            if ($i+1 == count($temp)){ //If edited item is last in list, dont move it down.
                return false;
            }

            $res1 = $this->db->editPositionPlaylistVideo($m_playlistid, $m_videoid, $editedItem['position']+1); //Move selected item down one

            $res2 = $this->db->editPositionPlaylistVideo($m_playlistid, $temp[$i+1]['videoid'], $temp[$i+1]['position']-1); //Move item "below" one up

        } else { //Move the pos towards the top (1)

            $i = -1;
            foreach($temp as $value) { //Find index of edited item in all of videos
                $i++;
                if ($value === $editedItem) {
                    break;
                } 
            }

            if ($i == -1) { //$i is only -1 if a match was not found. This should never happen!
                return false;
            }

            if ($editedItem['position'] == 1){ //If edited item is first in the list. Dont move it up
                return false;
            }

            $res1 = $this->db->editPositionPlaylistVideo($m_playlistid, $m_videoid, $editedItem['position']-1); //Move selected item up one

            $res2 = $this->db->editPositionPlaylistVideo($m_playlistid, $temp[$i-1]['videoid'], $temp[$i-1]['position']+1); //Move item "above" one down

        }

        return true;

    }

    public function insertPlaylist($m_ownerId, $m_name, $m_description, $m_thumbnail){

        if(!$m_thumbnail['name']) {
            echo("<center><strong>Du må laste opp en thumbnail</strong></center>");
            return false;
        }

        $thumb_path = Playlist::$target_dir . basename($m_thumbnail["name"]);

        $thumb_file_type = strtolower(pathinfo($thumb_path, PATHINFO_EXTENSION));

        /* TODO : Return meaningful error for all of these, for now, debug echos */
        if (file_exists($thumb_path)) {
            echo "FILE EXISTS!\n";
            print_r($thumb_path);
            return false;
        }

        if ($thumb_file_type != "jpg" && $thumb_file_type != "png" && $thumb_file_type != "jpeg" && $thumb_file_type != "gif") {
            echo "Thumb format must be jpg, png, jpeg or gif";
            return false;
        }

        /* Resize Thumbnail to 320x180 */
        $this->thumbnailResize($m_thumbnail, 320, 180, $thumb_path);

        return $this->db->insertPlaylist($m_ownerId, $m_name, $m_description, $thumb_path);
    }

    public function returnPlaylist($m_id){
        return $this->db->returnPlaylist($m_id);
    }

    public function returnPlaylists($m_id){
        return $this->db->returnPlaylists($m_id);
    }

    public function returnVideos($m_userid){
        return $this->db->returnVideos($m_userid);
    }

    public function returnPlaylistVideos($m_playlistId){
        return $this->db->returnPlaylistVideos($m_playlistId);
    }

    public function updatePlaylist($m_id, $m_ownerId, $m_name, $m_description, $m_thumbnail){

        if (!$m_thumbnail['name']) { 
            return $this->db->updatePlaylist($m_id, $m_ownerId, $m_name, $m_description);
        } else {
            $thumb_path = Playlist::$target_dir . basename($m_thumbnail["name"]);

            $thumb_file_type = strtolower(pathinfo($thumb_path, PATHINFO_EXTENSION));
    
            /* TODO : Return meaningful error for all of these, for now, debug echos */
            if (file_exists($thumb_path)) {
                echo "FILE EXISTS!\n";
                print_r($thumb_path);
                return false;
            }
    
            if ($thumb_file_type != "jpg" && $thumb_file_type != "png" && $thumb_file_type != "jpeg" && $thumb_file_type != "gif") {
                echo "Thumb format must be jpg, png, jpeg or gif";
                return false;
            }
    
            /* Resize Thumbnail to 320x180 */
            $this->thumbnailResize($m_thumbnail, 320, 180, $thumb_path);

            return $this->db->updatePlaylist($m_id, $m_ownerId, $m_name, $m_description, $thumb_path);
        }
    
    }

    public function returnAllPlaylists(){ //Should only be used on sites where all playlists should be shown!
        return $this->db->returnAllPlaylists();
    }

    public function countSubscribers($m_playlistid){
        return $this->db->countSubscribers($m_playlistid);
    }

    public function returnSubscriptionStatus($m_playlistid, $m_userid){
        $temp = $this->db->returnSubscriptionStatus($m_playlistid, $m_userid);
        if ($temp['userid'] == $m_userid) {
            return true;
        } else {
            return false;
        }
    }

    public function subscribeToPlaylist($m_playlistid, $m_userid){
        return $this->db->subscribeToPlaylist($m_playlistid, $m_userid);
    }

    public function unsubscribeToPlaylist($m_playlistid, $m_userid){
        return $this->db->unsubscribeToPlaylist($m_playlistid, $m_userid);
    }

    public function getSubscribedPlaylists($m_userid){
        $temp = $this->db->getSubscribedPlaylists($m_userid);
        
        if (!empty($temp)) {
            foreach ($temp as &$value) {
                $playlist = $this->db->returnPlaylist($value['playlistid']);
                //array_push($return, $playlist);
                $return[] = $playlist; //Get subarray of the temp array
            }

            return $return;

        } else {
            return false;
        }

    }

    public function thumbnailResize($thumbnail, $new_width, $new_height, $output_path){
        $content = file_get_contents($thumbnail["tmp_name"]);
        
        list($old_width, $old_height, $type, $attr) = getimagesize($thumbnail["tmp_name"]);
        
        $src_img = imagecreatefromstring(file_get_contents($thumbnail["tmp_name"]));
        $dst_img = imagecreatetruecolor($new_width, $new_height);
        
        /* Copy and store */
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);
        imagepng($dst_img, $output_path);
    
        /* Clean up */
        imagedestroy($src_img);
        imagedestroy($dst_img);        
    }


}


?>