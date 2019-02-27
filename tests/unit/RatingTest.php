<?php 
require_once './classes/db.php';
require_once './classes/video.php';
require_once './classes/user.php';
require_once './classes/rating.php';

class RatingTest extends \Codeception\Test\Unit
{

    //User
 private $userid = "1";
 private $userid_alt = "2";
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

        $this->tester->assertTrue($result);
    }

    public function testUpdateRating()
    {
        $this->tester->haveInDatabase('users',['id' => $this->userid, 'name' => $this->name, 'email' => $this->email, 'password' => $this->password]);
        $this->tester->haveInDatabase('video',['id' => $this->videoid, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);
        $this->tester->haveInDatabase('rating',['userid' => $this->userid, 'videoid' => $this->videoid, 'rating' => $this->rating]);
    
        $rating = new Rating();
        $result = $rating->updateRating($this->userid, $this->videoid, "2");

        $updated_rating = new Rating();
        $new_rating = $updated_rating->getRating($this->userid, $this->videoid);

        $this->tester->assertEquals("2", $new_rating[0]);
    }

    public function testGetRating()
    {
        $this->tester->haveInDatabase('users',['id' => $this->userid, 'name' => $this->name, 'email' => $this->email, 'password' => $this->password]);
        $this->tester->haveInDatabase('video',['id' => $this->videoid, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);
        $this->tester->haveInDatabase('rating',['userid' => $this->userid, 'videoid' => $this->videoid, 'rating' => $this->rating]);
    
        $rating = new Rating();
        $ratinginfo = $rating->getRating($this->userid, $this->videoid);

        $this->tester->assertEquals($ratinginfo[0], $this->rating);
    

    }

    public function testGetAllRatings()
    {
        $this->tester->haveInDatabase('users',['id' => $this->userid, 'name' => $this->name, 'email' => $this->email, 'password' => $this->password]);
        $this->tester->haveInDatabase('users',['id' => $this->userid_alt, 'name' => $this->name, 'email' => $this->email . "s", 'password' => $this->password]);
        $this->tester->haveInDatabase('video',['id' => $this->videoid, 'userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course, 'thumbnail_path' => $this->thumbnail, 'video_path' => $this->video]);
        $this->tester->haveInDatabase('rating',['userid' => $this->userid, 'videoid' => $this->videoid, 'rating' => $this->rating]);
        $this->tester->haveInDatabase('rating',['userid' => $this->userid_alt, 'videoid' => $this->videoid, 'rating' => $this->rating]);

        $res = new Rating();
        $ratings = $res->getAllRatings($this->videoid);
   
        /* Assert at vi fikk ut 2 ratings av de to vi har */
        $this->tester->assertEquals(sizeof($ratings), 2);
    }

    public function testGetTotalRatings()
    {

    }
}