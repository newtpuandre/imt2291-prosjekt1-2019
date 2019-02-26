<?php 

require_once './classes/db.php';
require_once './classes/video.php';
require_once './classes/user.php';

class VideoTest extends \Codeception\Test\Unit
{
     //Test related variables

    //User
    private $userid = "1";
    private $name = "name namesen";
    private $email = "test@test.test";
    private $password = "something";
    private $privileges = "2";
    private $isTeacher = true;

    //Video    
    private $videoid = "1";
    private $title = "testvideo";
    private $desc = "desc";
    private $topic = "topic";
    private $course = "course";
    private $thumbnail ="https://via.placeholder.com/150";
    private $video = "testVideo";

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

    // tests
    

    public function testUploadVideo()
    {

    }

    public function testUpdateVideo()
    {
        
    }

    public function testGetAllUserVideos()
    {
        
    }

    public function testGetAllVideosWithLecturers()
    {
   

    }

    public function testGetVideo()
    {        
        $this->tester->haveInDatabase('users',['id' => $this->userid, 'name' => $this->name, 'email' => $this->email, 'password' => $this->password]);
        $this->tester->haveInDatabase('video',['id' => $this->videoid, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);
    
        $video = new Video();
        $videoinfo = $video->getVideo($this->videoid);

        $this->assertEquals($videoinfo[0]['description'], $this->desc);
        $this->assertEquals($videoinfo[0]['title'], $this->title);
        $this->assertEquals($videoinfo[0]['thumbnail_path'], $this->thumbnail);
        $this->assertEquals($videoinfo[0]['id'], $this->userid);
        $this->assertEquals($videoinfo[0]['userid'], $this->userid);
        $this->assertEquals($videoinfo[0]['video_path'], $this->video);
        $this->assertEquals($videoinfo[0]['topic'], $this->topic);
        $this->assertEquals($videoinfo[0]['course'], $this->course);


    }

    public function testGetVideoLecturer()
    {
        $this->tester->haveInDatabase('users',['id' => $this->userid, 'name' => $this->name, 'email' => $this->email, 'password' => $this->password]);
        $this->tester->haveInDatabase('video',['id' => $this->videoid, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);
    
        $video = new Video();
        $videoinfo = $video->getVideoLecturer($this->videoid);

        $this->assertEquals($videoinfo[0]['name'], $this->name);

    }

    public function testDeleteVideo()
    {
        
    }

    public function testThumbnailResize()
    {
        
    }

    public function testSearchForVideo()
    {
        
    }

    public function testGetAllVideoCourses()
    {
        
    }

    public function testGetNewVideos()
    {
        
    }

    public function testSearchVideoCourse()
    {
        
    }
}