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
        $sql = 'SELECT id, name, email, picture_path, privileges FROM users WHERE email=:email';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':email', $m_email);
        $sth->execute();
        if ($row = $sth->fetch()) {
            return $row;
            
        } else {
            return null;
        }
    }

    public function registerUser($m_name, $m_email,$m_password, $m_isTeacher) {

        $sql = 'INSERT INTO users (name, email, password, isTeacher) values (?, ?, ?, ?)';
        $sth = $this->dbh->prepare($sql);
        // Use password_hash to encrypt password : http://php.net/manual/en/function.password-hash.php
        $sth->execute (array ($m_name, $m_email,
                          password_hash($m_password, PASSWORD_DEFAULT),$m_isTeacher));
        if ($sth->rowCount()==1) {
            return true;
        } else {
            return false;
        }
    }

    public function updateUser($m_id, $m_name, $m_email, $m_password, $m_picture){
      
        $sql = 'UPDATE users SET name=:name, email=:email, password=:password, picture_path=:picture WHERE id=:id';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':id', $m_id);
        $sth->bindParam(':name', $m_name);
        $sth->bindParam(':email', $m_email);
        $sth->bindParam(':password', $m_password);
        $sth->bindParam(':picture', $m_picture);

        $sth->execute();
        if ($row = $sth->fetch()) {
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

        if ($row = $sth->fetchAll()) {
            return $row;
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
    
    public function updateVideo($m_videoid, $m_title, $m_description, $m_topic, $m_course){

        $sql = 'UPDATE video SET title =:title, description=:description, topic=:topic, course=:course WHERE id=:videoid';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':title', $m_title);
        $sth->bindParam(':videoid', $m_videoid);
        $sth->bindParam(':description', $m_description);
        $sth->bindParam(':topic', $m_topic);
        $sth->bindParam(':course', $m_course);

        $sth->execute();
        if ($row = $sth->fetch()) {
            return true;
        } else {
            return false;
        }
    }

    
    public function searchVideo($m_prompt){
        $sql = 'SELECT * FROM video WHERE title LIKE :prompt OR description LIKE :prompt OR topic LIKE :prompt OR course LIKE :prompt';
        $sth = $this->dbh->prepare ($sql);
        $param = "%" . $m_prompt . "%";
        $sth->bindParam(':prompt', $param);

        $sth->execute();

        if ($rows = $sth->fetchAll()) {
            return $rows;
        } else {
            return false;
        }
    }

    public function searchVideoCourse($m_prompt){
        $sql = 'SELECT COUNT(*), course, id, topic, time FROM video WHERE course LIKE :prompt';
        $sth = $this->dbh->prepare ($sql);
        $param = "%" . $m_prompt . "%";
        $sth->bindParam(':prompt', $param);

        $sth->execute();

        if ($rows = $sth->fetchAll()) {
            return $rows;
        } else {
            return false;
        }
    }

    public function returnAllCourses(){
        $sql = 'SELECT COUNT(*), course,id, topic, time FROM video GROUP BY course';
        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        if ($rows = $sth->fetchAll()) {
            return $rows;
        } else {
            return false;
        }
    }

    public function updateThumbnail($m_videoid, $m_thumb_path){

        $sql = 'UPDATE video SET thumbnail_path=:thumbnail_path WHERE id=:videoid';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':thumbnail_path', $m_thumb_path);
        $sth->bindParam(':videoid', $m_videoid);

        $sth->execute();
        if ($row = $sth->fetch()) {
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
        $sql = 'SELECT id, name, description, thumbnail, date FROM playlists WHERE ownerId=:id';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':id', $m_id);
        $sth->execute();
        if ($rows = $sth->fetchAll()) {
            return $rows;
        } else {
            return null;
        }
    }

    public function insertPlaylist($m_ownerId,$m_name,$m_description, $m_thumbnail){
        $sql = 'INSERT INTO playlists (ownerId , name, description, thumbnail) values (?, ?, ?, ?)';
        $sth = $this->dbh->prepare($sql);
        $sth->execute (array ($m_ownerId, $m_name, $m_description, $m_thumbnail));
        if ($sth->rowCount()==1) {
            return true;
        } else {
            return false;
        }
    }

    public function returnPlaylist($m_id){ //Returns a single playlist with a specific id
        $sql = 'SELECT id, ownerId, name, description, date, thumbnail FROM playlists WHERE id=:id';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':id', $m_id);
        $sth->execute();
        if ($row = $sth->fetch()) {
            return $row;
        } else {
            return null;
        }
    }

    public function updatePlaylist($m_id, $m_ownerId, $m_name, $m_description, $m_thumbnail = null){

        if ($m_thumbnail) {
            $sql = 'UPDATE playlists SET name = :name, description= :description, thumbnail=:thumbnail WHERE ownerId=:ownerId AND id=:id';
        } else {
            $sql = 'UPDATE playlists SET name = :name, description= :description WHERE ownerId=:ownerId AND id=:id';
        }


        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':name',$m_name);
        $sth->bindParam(':description', $m_description);
        $sth->bindParam(':ownerId', $m_ownerId);
        $sth->bindParam(':id', $m_id);

        if ($m_thumbnail){
            $sth->bindParam(':thumbnail', $m_thumbnail);
        }

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

    public function countSubscribers($m_playlistId){
        $sql = 'SELECT count(*) AS numSubs FROM subscriptions WHERE playlistid=:playlistid';
        $sth = $this->dbh->prepare($sql);

        $sth->bindParam(':playlistid', $m_playlistId);
        $sth->execute();

        if ($row = $sth->fetch()) {
            return $row;
        } else {
            return false;
        }

    }

    public function returnSubscriptionStatus($m_playlistid, $m_userid){
        $sql = 'SELECT userid FROM subscriptions WHERE playlistid=:playlistid AND userid=:userid';
        $sth = $this->dbh->prepare($sql);

        $sth->bindParam(':playlistid', $m_playlistid);
        $sth->bindParam(':userid', $m_userid);
        $sth->execute();

        if ($row = $sth->fetch()) {
            return $row;
        } else {
            return false;
        }
    }

    public function getSubscribedPlaylists($m_userid) {
        $sql = 'SELECT id, userid, playlistid FROM subscriptions WHERE userid=:userid';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':userid', $m_userid);
        $sth->execute();
        if ($rows = $sth->fetchAll()) {
            return $rows;
        } else {
            return null;
        }
    }

    public function subscribeToPlaylist($m_playlistid, $m_userid){
        $sql = 'INSERT INTO subscriptions (playlistid , userid) values (?, ?)';
        $sth = $this->dbh->prepare($sql);
        $sth->execute (array ($m_playlistid, $m_userid));
        if ($sth->rowCount()==1) {
            return true;
        } else {
            return false;
        }
    }

    public function unsubscribeToPlaylist($m_playlistid, $m_userid) {
        $sql = 'DELETE FROM subscriptions WHERE playlistid=:playlistid AND userid=:userid';
        $sth = $this->dbh->prepare($sql);

        $sth->bindParam(':playlistid', $m_playlistid);
        $sth->bindParam(':userid', $m_userid);
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
        $sql = 'SELECT id, videoid, position FROM playlistvideos WHERE playlistid=:playlistid ORDER BY position ASC';
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
        $sql = 'SELECT id, videoid, position FROM playlistvideos WHERE playlistid=:playlistid AND videoid=:videoid';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':playlistid', $m_playlistId);
        $sth->bindParam(':videoid', $m_videoid);
        $sth->execute();
        if ($row = $sth->fetch()) {
            return $row;
        } else {
            return false;
        }
    }

    public function returnAllPlaylists(){
        $sql = 'SELECT id, ownerid, name, description, thumbnail, date FROM playlists';
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        if ($rows = $sth->fetchAll()) {
            return $rows;
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

    public function editPositionPlaylistVideo($m_playlistId, $m_videoId, $m_position) {
        $sql = 'UPDATE playlistvideos SET position = :position WHERE videoid=:videoid AND playlistid=:playlistid';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':position', $m_position);
        $sth->bindParam(':videoid', $m_videoId);
        $sth->bindParam(':playlistid', $m_playlistId);
        $sth->execute();
        if ($row = $sth->fetch()) {
            return true;
        } else {
            return false;
        }
    }

    public function newComment($m_user, $m_video, $m_comment) {
        $sql = 'INSERT INTO comment (userid, videoid, comment) values (?, ?, ?)';
        $sth = $this->dbh->prepare($sql);
        $sth->execute(array($m_user, $m_video, $m_comment));

        if ($sth->rowCount()==1) {
            return true;
        } else {
            return false;
        }
    }

    public function returnAllComments($m_videoid){
      
        $sql = 'SELECT users.name, users.picture_path, comment.comment FROM comment 
        JOIN users ON comment.userid = users.id WHERE videoid=:videoid ORDER BY comment.id DESC';
        $sth = $this->dbh->prepare($sql);
        $sth->bindParam(':videoid', $m_videoid);

        $sth->execute();
        if ($rows = $sth->fetchAll()) {
            return $rows;
        } else {
            return null;
        }
    }

    public function newRating($m_user, $m_video, $m_rating) {
        $sql = 'INSERT INTO rating (userid, videoid, rating) values ( ?, ?, ?)';
        $sth = $this->dbh->prepare($sql);
        $sth->execute(array($m_user, $m_video, $m_rating));

        if ($sth->rowCount()==1) {
            return true;
        } else {
            return false;
        }
    }

    public function returnAllRatings($m_videoid){
        $sql = 'SELECT rating FROM rating WHERE videoid=:videoid';
        $sth = $this->dbh->prepare($sql);
        $sth->bindParam(':videoid', $m_videoid);

        $sth->execute();
        if ($rows = $sth->fetchAll()) {
            return $rows;
        } else {
            return null;
        }
    }

    public function returnTotalRatings($m_videoid){
        $sql = 'SELECT COUNT(*) FROM rating WHERE videoid=:videoid';
        $sth = $this->dbh->prepare($sql);
        $sth->bindParam(':videoid', $m_videoid);

        $sth->execute();
        if ($rows = $sth->fetchAll()) {
            return $rows;
        } else {
            return null;
        }
    }

    public function getNewVideos(){
        $sql = 'SELECT id, title, description, topic, course, thumbnail_path FROM video ORDER BY id DESC LIMIT 8';
        $sth = $this->dbh->prepare($sql);

        $sth->execute();
        if ($rows = $sth->fetchAll()) {
            return $rows;
        } else {
            return null;
        }
    }

    public function returnLecturerName($m_id){
        $sql = 'SELECT users.name FROM users JOIN video on video.userid = users.id WHERE video.id =:id;';
        $sth = $this->dbh->prepare($sql);
        $sth->bindParam(':id', $m_id);

        $sth->execute();
        if ($rows = $sth->fetchAll()) {
            return $rows;
        } else {
            return null;
        }
    }

}


?>