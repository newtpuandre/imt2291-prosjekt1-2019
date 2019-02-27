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
    private $videoid_alt ="2";
    private $title = "testvideo";
    private $title_alt = "testvideo2";
    private $desc = "desc";
    private $desc_alt = "desc2";
    private $topic = "topic";
    private $topic_alt = "topic2";
    private $course = "course";
    private $course_alt ="course2";
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
    

    public function testUpdateVideo()
    {
        $this->tester->haveInDatabase('users',['id' => $this->userid, 'name' => $this->name, 'email' => $this->email, 'password' => $this->password]);
        $this->tester->haveInDatabase('video',['id' => $this->videoid, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);
    
        $video = new Video();
        $result = $video->updateVideo($this->videoid, $this->title_alt, $this->desc_alt, $this->topic_alt, $this->course_alt, null);
        $updated_video = new Video();
        $new_video = $updated_video->getVideo($this->videoid);

        $this->tester->assertEquals($this->title_alt, $new_video[0]['title']);
    }

    public function testGetAllUserVideos()
    {
        $this->tester->haveInDatabase('users',['id' => $this->userid, 'name' => $this->name, 'email' => $this->email, 'password' => $this->password]);
        $this->tester->haveInDatabase('video',['id' => $this->videoid, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);
        $this->tester->haveInDatabase('video',['id' => $this->videoid_alt, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);

        $video = new Video();
        $videos = $video->getAllvideos($this->userid);
   
        /* Assert at vi fikk ut 2 ratings av de to vi har */
        $this->tester->assertEquals(sizeof($videos), 2);
    }

    public function testGetAllVideosWithLecturers()
    {
        $this->tester->haveInDatabase('users',['id' => $this->userid, 'name' => $this->name, 'email' => $this->email, 'password' => $this->password]);
        $this->tester->haveInDatabase('video',['id' => $this->videoid, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);
        $this->tester->haveInDatabase('video',['id' => $this->videoid_alt, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);

        $video = new Video();
        $videos = $video->getAllVideosWithLecturers($this->userid);
   
        /* Assert at vi fikk ut 2 ratings av de to vi har */
        $this->tester->assertEquals(sizeof($videos), 2);
        
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

    public function testSearch()
    {
               //Add a user inorder for the relation to be correct
               $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

               //Add a video in order for the relation to be correct
               $this->tester->haveInDatabase('video',['id' => $this->videoid, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);
       
               $video = new Video();
               $returnArray = $video->search($this->topic);
       
               $this->assertTrue($returnArray[0]['topic'] == $this->topic && $returnArray[0]['description'] == $this->desc);
    }

    public function testGetAllVideoCourses()
    {
        $this->tester->haveInDatabase('users',['id' => $this->userid, 'name' => $this->name, 'email' => $this->email, 'password' => $this->password]);
        $this->tester->haveInDatabase('video',['id' => $this->videoid, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);
        $this->tester->haveInDatabase('video',['id' => $this->videoid_alt, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course_alt, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);

        $video = new Video();
        $videos = $video->getAllVideoCourses();
   
        /* Assert at vi fikk ut 2 ratings av de to vi har */
        $this->tester->assertEquals(sizeof($videos), 2);
    }

    public function testGetNewVideos()
    {
        $this->tester->haveInDatabase('users',['id' => $this->userid, 'name' => $this->name, 'email' => $this->email, 'password' => $this->password]);
        $this->tester->haveInDatabase('video',['id' => $this->videoid, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);
        $this->tester->haveInDatabase('video',['id' => $this->videoid_alt, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);

        $video = new Video();
        $videos = $video->getNewVideos($this->userid);
   
        /* Assert at vi fikk ut 2 ratings av de to vi har */
        $this->tester->assertEquals(sizeof($videos), 2);
    }

    public function testSearchVideoCourse()
    {
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        //Add a video in order for the relation to be correct
        $this->tester->haveInDatabase('video',['id' => $this->videoid, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);
            
        $video = new Video();
        $returnArray = $video->searchVideoCourse($this->course);
               
        $this->assertTrue($returnArray[0]['course'] == $this->course);
    }
}