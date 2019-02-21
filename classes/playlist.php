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

        $this->db->deleteVideoFromPlaylist($m_playlistid, $m_videoid);

    }

}


?>