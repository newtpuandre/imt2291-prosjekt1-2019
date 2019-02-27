<?php 
require_once './classes/db.php';
require_once './classes/video.php';
require_once './classes/user.php';
require_once './classes/rating.php';

class RatingTest extends \Codeception\Test\Unit
{

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

  //Rating
  private $rating = "5";
  private $ratingid = "1";

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
    
    public function testAddRating()
    {
        $this->tester->haveInDatabase('users',['id' => $this->userid, 'name' => $this->name, 'email' => $this->email, 'password' => $this->password]);
        $this->tester->haveInDatabase('video',['id' => $this->videoid, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);
    
        $rating = new Rating();
        $result = $rating->addRating($this->userid, $this->videoid, $this->rating);

        $this->assertTrue($result);
    }

    public function testUpdateRating()
    {

    }

    public function testGetRating()
    {
        $this->tester->haveInDatabase('users',['id' => $this->userid, 'name' => $this->name, 'email' => $this->email, 'password' => $this->password]);
        $this->tester->haveInDatabase('video',['id' => $this->videoid, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);
        $this->tester->haveInDatabase('rating',['userid' => $this->userid, 'videoid' => $this->videoid, 'rating' => $this->rating]);
    
        $rating = new Rating();
        $ratinginfo = $rating->getRating($this->userid, $this->videoid);

        $this->assertEquals($ratinginfo[0], $this->rating);
    

    }

    public function testGetAllRatings()
    {

    }

    public function testGetTotalRatings()
    {

    }
}