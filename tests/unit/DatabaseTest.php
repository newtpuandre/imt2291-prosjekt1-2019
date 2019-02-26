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
    private $title = "testvideo";
    private $desc = "desc";
    private $topic = "topic";
    private $course = "course";



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

    public function testSearchVideo(){
        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

        $this->tester->haveInDatabase('video',['userid' => $this->userid, 'title' => $this->title, 'description' => $this->desc, 'topic' => $this->topic, 'course' => $this->course]);

        $returnArray = $this->db->searchVideo("testvideo");

        $this->assertTrue($returnArray[0]['title'] == $this->title);
    }

    

}