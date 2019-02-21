<?php
require_once 'db.php';

class Playlist
{

    private $title;
    private $description;

    private $db = null;

    function __construct() {
        //Initalize a new database connection
        $this->db = new DB();
    }

    function __destruct() {
    }

    public function returnTitle(){
        return $this->title;
    }

    public function returnDescription(){
        return $this->description;
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
            
            $this->db->addVideoToPlaylist($m_playlistid, $m_videoid, $lastPos);

        } else {
            return false;
        }

    }

    public function deletePlaylist($m_playlistid, $m_ownerid) {

        $this->db->deletePlaylist($m_playlistid, $m_ownerid);

    }

    public function deleteVideoFromPlaylist($m_playlistid, $m_videoid) {

        $pos = $this->db->returnPlaylistVideo($m_playlistid, $m_videoid);
        $temp = $this->db->returnPlaylistVideos($m_playlistid);

        $this->db->deleteVideoFromPlaylist($m_playlistid, $m_videoid);

        //REMEMBER TO KEEP POSITIONS IN ORDER
        //find and edit all videoes with pos > removed && do pos-1 > 1

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

        for ($j = $i; $j < count($temp); $j++) {
            $this->db->editPositionPlaylistVideo($m_playlistid, $temp[$j]['videoid'], $temp[$j]['position']-1); //Move selected item down one
        }

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

            $this->db->editPositionPlaylistVideo($m_playlistid, $m_videoid, $editedItem['position']+1); //Move selected item down one

            $this->db->editPositionPlaylistVideo($m_playlistid, $temp[$i+1]['videoid'], $temp[$i+1]['position']-1); //Move item "below" one up

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

            $this->db->editPositionPlaylistVideo($m_playlistid, $m_videoid, $editedItem['position']-1); //Move selected item up one

            $this->db->editPositionPlaylistVideo($m_playlistid, $temp[$i-1]['videoid'], $temp[$i-1]['position']+1); //Move item "above" one down

        }

    }

}


?>