<?php

require_once './classes/admin.php';

class DatabaseTest extends \Codeception\Test\Unit
{

    //Test related variables

    //User
    private $name = "name namesen";
    private $email = "test@test.test";
    private $password = "something";
    private $privileges = "2";
    private $isTeacher = true;

    //Video
    private $userid = "1";
    private $videoid = "1";
    private $title = "testvideo";
    private $desc = "desc";
    private $topic = "topic";
    private $course = "course";
    private $thumbnail ="testThumb";

    //Playlist
    private $playlistid = "1";
    private $ownerid = "1";
    private $playName = "playlistname";
    private $playDesc = "playlistDesc";
    private $playThumb = "thumbnail";

    //Comment
    private $comment = "Comment";



    private $db = null;
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
        //Create db connection
        $this->db = new DB();
        
    }

    protected function _after()
    {

    }

    public function testFindUser()
    {

        //Insert user into database
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password, 'privileges' => $this->privileges]);

        //Check if function returns user
        $returnArray = $this->db->findUser($this->email);
        $this->assertTrue($returnArray['email'] == $this->email);

    }

    public function testUpdateUser(){
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $hashedPassword]);

        $new_name = "name test";
        $new_email = "testemail";
        $new_picture = "test_picture";
        $password_hash = password_hash("somethingNew", PASSWORD_DEFAULT);

        $this->db->updateUser("1", $new_name, $new_email, $password_hash, $new_picture);

        $this->tester->seeInDatabase('users',['id' => "1", 'name' => $new_name, 'email' => $new_email, 'password' =>  $password_hash, 'picture_path' => $new_picture]);
    }

    public function testRegisterUser()
    {

        //Register the user
        $this->db->registerUser($this->name, $this->email, $this->password, $this->isTeacher);

        //User is found in database
        $this->tester->seeInDatabase('users',['name' => $this->name, 'email' => $this->email]);

    }

    public function testLoginUser()
    {
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $hashedPassword]);

        $this->assertTrue($this->db->loginUser($this->email, $this->password));

    }

    public function testGatherUsers()
    {

        //Insert two users
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password, 'privileges' => $this->privileges]);

        //Return user(s)
        $returnArray = $this->db->gatherUsers();

        //User is in db
        $this->assertTrue($returnArray[0]['email'] == $this->email);
    }

    public function testNewVideo()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);
        
        $result = $this->db->newVideo($this->userid, $this->title,"desc","topic","course","nothing","nothing");

        $this->assertTrue($result);

        $this->tester->seeInDatabase('video', ['userid' => $this->userid, 'title' => $this->title]);

    }

    public function testReturnVideos()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title]);

        $returnArray = $this->db->returnVideos($this->userid);

        $this->assertTrue($returnArray[0]['title'] == $this->title);
    }

    public function testReturnVideo()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);
        
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title]);

        $returnArray = $this->db->returnVideo("1");

        $this->assertTrue($returnArray[0]['title'] == $this->title);

    }

    public function testReturnAllVideos()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title]);

        $returnArray = $this->db->returnAllVideos();

        $this->assertTrue($returnArray[0]['title'] == $this->title);
    }

    public function testDeleteVideo() 
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title]);

        $this->tester->seeInDatabase('video',['userid' => $this->userid, 'title' => $this->title]);

        $this->db->deleteVideo("1");

        $this->tester->dontSeeInDatabase('video',['userid' => $this->userid, 'title' => $this->title]);

    }

    public function testUpdateVideo()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course]);

        $this->db->updateVideo("1","newTitle","newDesc","newTopic","newCourse");

        $this->tester->seeInDatabase('video',['userid' => $this->userid, 'title' => "newTitle", 'description' => "newDesc", 'topic' => "newTopic", 'course' => "newCourse"]);

    }

    public function testSearchVideo()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course]);

        $returnArray = $this->db->searchVideo("testvideo");

        $this->assertTrue($returnArray[0]['title'] == $this->title);
    }

    public function testSearchVideoCourse()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course]);

        $returnArray = $this->db->searchVideoCourse($this->course);

        $this->assertTrue($returnArray[0]['course'] == $this->course);

    }

    public function testReturnAllCourses()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course]);

        $returnArray = $this->db->returnAllCourses();

        $this->assertTrue($returnArray[0]['course'] == $this->course);
    }
    
    public function testUpdateThumbnail()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);

        $return = $this->db->updateThumbnail("1", "uploads/testimage.png");

        $this->tester->seeInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => 'uploads/testimage.png']);

    }

    public function testUpdatePrivileges()
    {
        //Add a user
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        $this->db->updatePrivileges($this->email, "2");

        $this->tester->seeInDatabase('users', ['email' => $this->email, 'privileges' => "2"]);
    }

    public function testReturnPlaylists()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        $returnArray = $this->db->returnPlaylists($this->ownerid);

        $this->assertTrue($returnArray[0]['name'] == $this->playName && $returnArray[0]['description'] == $this->playDesc);
    }

    public function testInsertPlaylist()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);
        
        $this->db->insertPlaylist($this->ownerid, $this->playName, $this->playDesc, $this->playThumb);

        $this->tester->seeInDatabase('playlists',['name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);
    }

    
    public function testReturnPlaylist()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        $return = $this->db->returnPlaylist($this->ownerid);

        $this->assertTrue($return['name'] == $this->playName && $return['description'] == $this->playDesc);
    }


    public function testUpdatePlaylist()
    {
        $newName = "test";
        $newDesc = "testDesc";
        $newThumb = "testThumbnail";
        
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        $this->db->updatePlaylist("1", $this->ownerid, $newName, $newDesc, $newThumb);

        $this->tester->seeInDatabase('playlists',['name' => $newName, 'description' => $newDesc, 'thumbnail' => $newThumb]);
    }

    public function testDeletePlaylist()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        $this->db->deletePlaylist($this->playlistid, $this->ownerid);

        $this->tester->dontSeeInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

    }

    public function testCountSubscribers()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        //Add a subscription
        $this->tester->haveInDatabase('subscriptions', ['userid' => $this->userid, 'playlistid' => $this->playlistid]);

        $return = $this->db->countSubscribers($this->playlistid);

        $this->assertTrue($return['numSubs'] == "1");
    }

    public function testReturnSubscriptionStatus()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        //Add a subscription
        $this->tester->haveInDatabase('subscriptions', ['userid' => $this->userid, 'playlistid' => $this->playlistid]);

        $return = $this->db->ReturnSubscriptionStatus($this->userid, $this->playlistid);

        $this->assertTrue($return['userid'] == $this->userid);
    }

    public function testGetSubscribedPlaylists()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        //Add a subscription
        $this->tester->haveInDatabase('subscriptions', ['userid' => $this->userid, 'playlistid' => $this->playlistid]);

        $returnArray = $this->db->GetSubscribedPlaylists($this->userid);

        $this->assertTrue($returnArray[0]['userid'] == $this->userid && $returnArray[0]['playlistid'] == $this->playlistid);
    }

    public function testSubscribeToPlaylist()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        $this->db->subscribeToPlaylist($this->playlistid, $this->ownerid);

        $this->tester->seeInDatabase('subscriptions', ['userid' => $this->userid, 'playlistid' => $this->playlistid]);
    }

    public function testUnsubscribeToPlaylist()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        //Add a subscription
        $this->tester->haveInDatabase('subscriptions', ['userid' => $this->userid, 'playlistid' => $this->playlistid]);

        $this->db->unsubscribeToPlaylist($this->playlistid, $this->ownerid);

        $this->tester->dontSeeInDatabase('subscriptions', ['userid' => $this->userid, 'playlistid' => $this->playlistid]);
    }

    public function testDeleteVideoFromPlaylist()
    {

        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);

        $this->tester->haveInDatabase('playlistvideos', ['videoid' => $this->videoid, 'playlistid' => $this->playlistid, 'position' => '1']);

        $this->db->deleteVideoFromPlaylist($this->playlistid, $this->videoid);

        $this->tester->dontSeeInDatabase('playlistvideos', ['videoid' => $this->videoid, 'playlistid' => $this->playlistid, 'position' => '1']);

    }

    public function testAddVideoToPlaylist()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);
    
        $this->db->addVideoToPlaylist($this->playlistid, $this->videoid, "1");

        $this->tester->seeInDatabase('playlistvideos', ['videoid' => $this->videoid, 'playlistid' => $this->playlistid, 'position' => '1']);
    }

    public function testReturnPlaylistVideos()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);

        $this->tester->haveInDatabase('playlistvideos', ['videoid' => $this->videoid, 'playlistid' => $this->playlistid, 'position' => '1']);

        $returnArray = $this->db->returnPlaylistVideos($this->playlistid);

        $this->assertTrue($returnArray[0]['videoid'] == $this->videoid && $returnArray[0]['position'] == "1");
    }

    public function testReturnPlaylistVideo()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);
        
        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);
        
        $this->tester->haveInDatabase('playlistvideos', ['videoid' => $this->videoid, 'playlistid' => $this->playlistid, 'position' => '1']);
        
        $return = $this->db->returnPlaylistVideo($this->playlistid, $this->videoid);
        
        $this->assertTrue($return['videoid'] == $this->videoid && $return['position'] == "1");
    }

    public function testReturnAllPlaylists()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        $returnArray = $this->db->returnAllPlaylists();

        $this->assertTrue($returnArray[0]['ownerid'] == $this->ownerid && $returnArray[0]['name'] == $this->playName);
    }

    public function testReturnNewestPlaylistVideo() 
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['id' => "1", 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);
        $this->tester->haveInDatabase('video',['id' => "2", 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);

        $this->tester->haveInDatabase('playlistvideos', ['videoid' => "1", 'playlistid' => $this->playlistid, 'position' => '1']);
        $this->tester->haveInDatabase('playlistvideos', ['videoid' => "2", 'playlistid' => $this->playlistid, 'position' => '2']);

        $return = $this->db->returnNewestPlaylistVideo($this->playlistid);

        $this->assertTrue($return['videoid'] == "2" && $return['position'] == "2");
    }

    public function testEditPositionPlaylistVideo()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);

        $this->tester->haveInDatabase('playlistvideos', ['videoid' => $this->playlistid, 'playlistid' => $this->playlistid, 'position' => '1']);

        $this->db->editPositionPlaylistVideo($this->playlistid, $this->videoid, "2");

        $this->tester->seeInDatabase('playlistvideos', ['videoid' => $this->playlistid, 'playlistid' => $this->playlistid, 'position' => '2']);
    }

    public function testNewComment()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);

        $this->db->newComment($this->userid, $this->videoid, $this->comment);

        $this->tester->seeInDatabase('comment',['userid' => $this->userid, 'videoid' => $this->videoid, 'comment' => $this->comment]);
    }

    public function testReturnAllComments()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);

        $this->tester->haveInDatabase('comment',['userid' => $this->userid, 'videoid' => $this->videoid, 'comment' => $this->comment]);

        $returnArray = $this->db->returnAllComments($this->videoid);

        $this->assertTrue($returnArray[0]['comment'] == $this->comment && $returnArray[0]['name'] == $this->name);
    }

    public function testNewRating()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);

        $this->db->newRating($this->userid, $this->videoid, "5");

        $this->tester->seeInDatabase('rating',['userid' => $this->userid, 'videoid' => $this->videoid, 'rating' => "5"]);
    }

    public function testReturnAllRatings()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);

        $this->tester->haveInDatabase('rating',['userid' => $this->userid, 'videoid' => $this->videoid, 'rating' => "5"]);

        $returnArray = $this->db->returnAllRatings($this->videoid);

        $this->assertTrue($returnArray[0]['rating'] == "5");
    }

    public function testReturnTotalRatings()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);
        $this->tester->haveInDatabase('users',['name' => "test2", 'email' => "test@test2.t", 'password' => $this->password]);

        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);

        $this->tester->haveInDatabase('rating',['userid' => $this->userid, 'videoid' => $this->videoid, 'rating' => "5"]);
        $this->tester->haveInDatabase('rating',['userid' => "2", 'videoid' => $this->videoid, 'rating' => "2"]);

        $return = $this->db->returnTotalRatings($this->videoid);

        $this->assertTrue($return[0]['COUNT(*)'] == "2");
    }

    public function testGetNewVideos()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);

        $returnArray = $this->db->getNewVideos();

        $this->assertTrue($returnArray[0]['title'] == $this->title && $returnArray[0]['description'] == $this->desc);

    }

    public function testReturnLecturerName()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);

        $returnArray = $this->db->returnLecturerName($this->userid);

        $this->assertTrue($returnArray[0]['name'] == $this->name);

    }

    public function testReturnAllVideosWithLecturers()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);

        $returnArray = $this->db->returnAllVideosWithLecturers();

        $this->assertTrue($returnArray[0]['name'] == $this->name && $returnArray[0]['title'] == $this->title);
    }

}