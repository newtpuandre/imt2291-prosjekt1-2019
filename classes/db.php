<?php


class DB
{
    //Edit the variables below to the correct database settings
    private static $db=null;
    private $dsn = 'mysql:dbname=prosjekt1;host=127.0.0.1'; 
    private $user = 'root';
    private $password = '';
    private $dbh = null;

    function __construct() {
        //Attempt connection to the database

        try {
            $this->dbh = new PDO($this->dsn, $this->user, $this->password);
        } catch (PDOException $e) {
            // NOTE IKKE BRUK DETTE I PRODUKSJON
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    function __destruct() {
       
    }

    public function getDBConnection() {
        return $this->dbh;
    }

    public function findUser($m_email){
        $sql = 'SELECT id, email, privileges FROM users WHERE email=:email';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':email', $m_email);
        $sth->execute();
        if ($row = $sth->fetch()) {
            return $row;
            
        } else {
            return null;
        }
    }

    public function registerUser($m_email,$m_password, $m_isTeacher) {

        $sql = 'INSERT INTO users (email , password, isTeacher) values (?, ?, ?)';
        $sth = $this->dbh->prepare($sql);
        // Use password_hash to encrypt password : http://php.net/manual/en/function.password-hash.php
        $sth->execute (array ($m_email,
                          password_hash($m_password, PASSWORD_DEFAULT),$m_isTeacher));
        if ($sth->rowCount()==1) {
            return true;
        } else {
            return false;
        }
    }

    public function loginUser($m_email,$m_password){

        $sql = 'SELECT password, id FROM users WHERE email=:email';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':email', $m_email);
        $sth->execute();
        if ($row = $sth->fetch()) { // get id and hashed password for given user
            // Use password_verify to check given password : http://php.net/manual/en/function.password-verify.php
            if (password_verify($m_password, $row['password'])) {
                return true;
    
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function gatherUsers(){
        $sql = 'SELECT email, privileges, isTeacher FROM users ORDER BY id DESC ';
        $sth = $this->dbh->prepare ($sql);
        $sth->execute();
        if ($rows = $sth->fetchAll()) {
            return $rows;            
        } else {
            return null;
        }
    }

    public function newVideo($user, $title, $desc, $topic, $course, $thumb_path, $video_path) {
        $sql = 'INSERT INTO video (userid, title, description, topic, course, thumbnail_path, video_path) values (?, ?, ?, ?, ?, ?, ?)';
        $sth = $this->dbh->prepare($sql);
        $sth->execute(array($user, $title, $desc, $topic, $course, $thumb_path, $video_path));

        if ($sth->rowCount()==1) {
            return true;
        } else {
            return false;
        }
    }

    public function returnVideos($m_userid, $m_startnum = "" ,$m_endnum = ""){ //Returns all videos a user "owns". Can limit with $start and $end
        $sql = 'SELECT id, userid, title, description, topic, course, time, thumbnail_path, video_path FROM video WHERE userid=:userid ORDER BY time DESC';

        if( $m_startnum != "" && $m_endnum != "") {
            $sql .= ' LIMIT :start, :end';
        } 

        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':userid', $m_userid);

        if($m_startnum != "" && $m_endnum != "") {
            $sth->bindParam(':start', $m_startnum);
            $sth->bindParam(':end', $m_endnum);
        }

        $sth->execute();
        if ($rows = $sth->fetchAll()) {
            return $rows;
        } else {
            return null;
        }
    }

    public function returnVideo($m_videoid){
        $sql = 'SELECT * FROM video WHERE id=:id';

        $sth = $this->dbh->prepare($sql);
        $sth->bindParam(':id', $m_videoid);

        $sth->execute();
        if ($rows = $sth->fetchAll()) {
            return $rows;
        } else {
            return null;
        }
    }

    public function returnAllVideos(){
        $sql = 'SELECT * FROM video ORDER BY time DESC';
        $sth = $this->dbh->prepare ($sql);

        $sth->execute();
        if ($rows = $sth->fetchAll()) {
            return $rows;
        } else {
            return null;
        }
    }

    public function deleteVideo($m_videoid){
        $sql = 'DELETE FROM video WHERE id=:videoid';
        $sth = $this->dbh->prepare($sql);

        $sth->bindParam(':videoid', $m_videoid);
        $sth->execute();

        if ($sth->rowCount()==1) {
            return true;
        } else {
            return false;
        }
    }

    public function updatePrivileges($m_email, $m_privilevel) {
        $sql = 'UPDATE users SET PRIVILEGES = :privileges WHERE email=:email';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':privileges',$m_privilevel);
        $sth->bindParam(':email', $m_email);
        $sth->execute();
        if ($row = $sth->fetch()) {
            return true;
        } else {
            return false;
        }
    }

    public function returnPlaylists($m_id){ //Returns ALL playlists
        $sql = 'SELECT id, name, description, date FROM playlists WHERE ownerId=:id';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':id', $m_id);
        $sth->execute();
        if ($rows = $sth->fetchAll()) {
            return $rows;
        } else {
            return null;
        }
    }

    public function insertPlaylist($m_ownerId,$m_name,$m_description){
        $sql = 'INSERT INTO playlists (ownerId , name, description) values (?, ?, ?)';
        $sth = $this->dbh->prepare($sql);
        $sth->execute (array ($m_ownerId, $m_name, $m_description));
        if ($sth->rowCount()==1) {
            return true;
        } else {
            return false;
        }
    }

    public function returnPlaylist($m_id, $m_ownerId){ //Returns a single playlist with a specific id
        $sql = 'SELECT id, ownerId, name, description, date FROM playlists WHERE id=:id AND ownerId=:ownerId';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':id', $m_id);
        $sth->bindParam(':ownerId', $m_ownerId);
        $sth->execute();
        if ($row = $sth->fetch()) {
            return $row;
        } else {
            return null;
        }
    }

    public function updatePlaylist($m_id, $m_ownerId, $m_name, $m_description){
        $sql = 'UPDATE playlists SET name = :name, description= :description WHERE ownerId=:ownerId AND id=:id';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':name',$m_name);
        $sth->bindParam(':description', $m_description);
        $sth->bindParam(':ownerId', $m_ownerId);
        $sth->bindParam(':id', $m_id);
        $sth->execute();
        if ($row = $sth->fetch()) {
            return true;
        } else {
            return false;
        }
    }

    public function deletePlaylist($m_playlistId,$m_ownerId){

        $sql = 'DELETE FROM playlists WHERE id=:id AND ownerId=:ownerId';
        $sth = $this->dbh->prepare($sql);

        $sth->bindParam(':id', $m_playlistId);
        $sth->bindParam(':ownerId', $m_ownerId);
        $sth->execute();

        if ($sth->rowCount()==1) {
            return true;
        } else {
            return false;
        }

    }

    public function deleteVideoFromPlaylist ($m_playlistId, $m_videoId){

        $sql = 'DELETE FROM playlistvideos WHERE videoid=:videoId AND playlistid=:playlistId';
        $sth = $this->dbh->prepare($sql);

        $sth->bindParam(':playlistId', $m_playlistId);
        $sth->bindParam(':videoId', $m_videoId);
        $sth->execute();

        if ($sth->rowCount()==1) {
            return true;
        } else {
            return false;
        }

    }

    public function AddVideoToPlaylist($m_playlistid, $m_videoid, $m_position)
    {
        $sql = 'INSERT INTO playlistvideos (videoid, playlistid, position) values (?, ?, ?)';
        $sth = $this->dbh->prepare($sql);
        $sth->execute(array($m_videoid,$m_playlistid,$m_position));

        if ($sth->rowCount()==1) {
            return true;
        } else {
            return false;
        }
    }

    public function returnPlaylistVideos($m_playlistId) {
        $sql = 'SELECT id, videoid FROM playlistvideos WHERE playlistid=:playlistid ORDER BY position ASC';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':playlistid', $m_playlistId);
        $sth->execute();
        if ($rows = $sth->fetchAll()) {
            return $rows;
        } else {
            return null;
        }
    }

    public function returnPlaylistVideo($m_playlistId, $m_videoid) { // Select ONE video from the playlist
        $sql = 'SELECT id, videoid FROM playlistvideos WHERE playlistid=:playlistid AND videoid=:videoid';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':playlistid', $m_playlistId);
        $sth->bindParam(':videoid', $m_videoid);
        $sth->execute();
        if ($row = $sth->fetch()) {
            return true;
        } else {
            return false;
        }
    }

    public function returnNewestPlaylistVideo($m_playlistId) {
        $sql = 'SELECT id, videoid, position FROM playlistvideos WHERE playlistid=:playlistid ORDER BY position DESC';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':playlistid', $m_playlistId);
        $sth->execute();
        if ($row = $sth->fetch()) {
            return $row;
        } else {
            return null;
        }
    }

    public function newComment($user, $video, $comment) {
        $sql = 'INSERT INTO comment (userid, videoid, comment) values (?, ?, ?)';
        $sth = $this->dbh->prepare($sql);
        $sth->execute(array($user, $video, $comment));

        if ($sth->rowCount()==1) {
            return true;
        } else {
            return false;
        }
    }

    public function returnAllComments($m_videoid){
        $sql = 'SELECT * FROM comment WHERE videoid=:videoid ORDER BY id DESC';
        $sth = $this->dbh->prepare($sql);
        $sth->bindParam(':videoid', $m_videoid);

        $sth->execute();
        if ($rows = $sth->fetchAll()) {
            return $rows;
        } else {
            return null;
        }
    }
}


?>