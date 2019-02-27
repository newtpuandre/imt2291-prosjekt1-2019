<?php 

require_once 'classes/playlist.php';

class PlaylistTest extends \Codeception\Test\Unit
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

    private $playlist;
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
        $this->playlist = new Playlist();
    }

    protected function _after()
    {
    }

    // tests
    public function testResolveVideos()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);

        $this->tester->haveInDatabase('playlistvideos', ['videoid' => $this->videoid, 'playlistid' => $this->playlistid, 'position' => '1']);

        $return = $this->playlist->resolveVideos($this->playlistid);

        $this->assertTrue($return[0]['title'] == $this->title && $return[0]['description'] == $this->desc);
    }

    public function testAddVideoToPlaylist()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);

        $this->playlist->addVideoToPlaylist($this->playlistid, $this->videoid);
        $this->playlist->addVideoToPlaylist($this->playlistid, "2");

        $this->tester->seeInDatabase('playlistvideos',['playlistid' => $this->playlistid, 'videoid' => $this->videoid, 'position' => '1']);
        $this->tester->seeInDatabase('playlistvideos',['playlistid' => $this->playlistid, 'videoid' => "2", 'position' => '2']);
    }

    public function testSearchForPlaylist()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        $returnArray = $this->playlist->searchForPlaylists($this->playName);

        $this->assertTrue($returnArray[0]['name'] == $this->playName && $returnArray[0]['description'] == $this->playDesc);

    }

    public function testDeletePlaylist()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        $this->playlist->deletePlaylist($this->playlistid, $this->ownerid);

        $this->tester->dontSeeInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

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

        $this->playlist->deleteVideoFromPlaylist($this->playlistid, $this->videoid);

        $this->tester->dontSeeInDatabase('playlistvideos', ['videoid' => $this->videoid, 'playlistid' => $this->playlistid, 'position' => '1']);

    }

    public function testEditPosition()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);
        
        $this->tester->haveInDatabase('playlistvideos', ['videoid' => $this->videoid, 'playlistid' => $this->playlistid, 'position' => '1']);
        $this->tester->haveInDatabase('playlistvideos', ['videoid' => "2", 'playlistid' => $this->playlistid, 'position' => '2']);

        $this->playlist->editPosition($this->playlistid, $this->videoid, true);

        $this->tester->seeInDatabase('playlistvideos', ['videoid' => $this->videoid, 'playlistid' => $this->playlistid, 'position' => '2']);

        $this->playlist->editPosition($this->playlistid, $this->videoid, false);

        $this->tester->seeInDatabase('playlistvideos', ['videoid' => $this->videoid, 'playlistid' => $this->playlistid, 'position' => '1']);
        
    }

    public function testReturnPlaylist()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        $return = $this->playlist->returnPlaylist($this->playlistid);

        $this->assertTrue($return['name'] == $this->playName && $return['description'] == $this->playDesc);
    }

    public function testReturnPlaylists()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        $return = $this->playlist->returnPlaylists($this->userid);

        $this->assertTrue($return[0]['name'] == $this->playName && $return[0]['description'] == $this->playDesc);
    }

    public function testReturnVideos()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a video inorder for the relation to be correct
        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail]);

        $returnArray = $this->playlist->returnVideos($this->userid);

        $this->assertTrue($returnArray[0]['title'] == $this->title && $returnArray[0]['description'] == $this->desc);
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

        $returnArray = $this->playlist->returnPlaylistVideos($this->playlistid);
    }

    public function testReturnAllPlaylists() 
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        $return = $this->playlist->returnAllPlaylists();

        $this->assertTrue($return[0]['name'] == $this->playName && $return[0]['description'] == $this->playDesc);
    }

    public function testCountSubscribers()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        $this->tester->haveInDatabase('subscriptions', ['userid' => $this->userid, 'playlistid' => $this->playlistid]);

        $count = $this->playlist->countSubscribers($this->playlistid);

        $this->assertTrue($count['numSubs'] == 1);

    }

    public function testSubscribeToPlaylist() 
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);
        
        $this->playlist->subscribeToPlaylist($this->playlistid, $this->userid);

        $this->tester->seeInDatabase('subscriptions', ['userid' => "1", 'playlistid' => $this->playlistid]);
    }

    public function testUnsubscribeToPlaylist() 
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        $this->tester->haveInDatabase('subscriptions', ['userid' => $this->userid, 'playlistid' => $this->playlistid]);

        $this->playlist->unsubscribeToplaylist($this->playlistid, $this->userid);

        $this->tester->dontSeeInDatabase('subscriptions', ['userid' => "1", 'playlistid' => $this->playlistid]);
    }

    public function testGetSubscribedPlaylists() 
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a playlist inorder for the relation to be correct
        $this->tester->haveInDatabase('playlists',['ownerid' =>  $this->ownerid, 'name' => $this->playName, 'description' => $this->playDesc, 'thumbnail' => $this->playThumb]);

        $this->tester->haveInDatabase('subscriptions', ['userid' => $this->userid, 'playlistid' => $this->playlistid]);

        $returnArray = $this->playlist->getSubscribedPlaylists($this->userid);

        $this->assertTrue($returnArray[0]['id'] == '1');

    }

}