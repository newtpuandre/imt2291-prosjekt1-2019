<?php

require_once './classes/admin.php';

class DatabaseTest extends \Codeception\Test\Unit
{
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
        $name = "name namesen";
        $email = "test@test.test";
        $password = "something";
        $privileges = "2";

        //Insert user into database
        $this->tester->haveInDatabase('users',['name' => $name, 'email' => $email, 'password' => $password, 'privileges' => $privileges]);

        //Check if function returns user
        $returnArray = $this->db->findUser($email);
        $this->assertTrue($returnArray['email'] == $email);

    }

    public function testUpdateUser(){
        $name = "name namesen";
        $email = "test@test.test";
        $password = "something";

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $this->tester->haveInDatabase('users',['name' => $name, 'email' => $email, 'password' => $hashedPassword]);

        
        $new_name = "name test";
        $new_email = "testemail";
        $new_picture = "test_picture";

        $password_hash = password_hash("somethingNew", PASSWORD_DEFAULT);

        $this->assertTrue($this->db->updateUser("1", $new_name, $new_email, $password_hash, $new_picture));

        $this->tester->seeInDatabase('users',['id' => "1", 'name' => $new_name, 'email' => $new_email, 'password' => $password_hash, 'picture_path' => $new_picture]);
    }

    public function testRegisterUser()
    {
        $name = "name namesen";
        $email = "test@test.test";
        $password = "something";
        $isTeacher = true;

        //Register the user
        $this->db->registerUser($name, $email, $password, $isTeacher);

        //User is found in database
        $this->tester->seeInDatabase('users',['name' => $name, 'email' => $email]);

    }

    public function testLoginUser()
    {
        $name = "name namesen";
        $email = "test@test.test";
        $password = "something";

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $this->tester->haveInDatabase('users',['name' => $name, 'email' => $email, 'password' => $hashedPassword]);

        $this->assertTrue($this->db->loginUser($email, $password));

    }

    public function testGatherUsers()
    {
        $name = "name namesen";
        $email = "test@test.test";
        $password = "something";
        $privileges = "2";

        //Insert two users
        $this->tester->haveInDatabase('users',['name' => $name, 'email' => $email, 'password' => $password, 'privileges' => $privileges]);


        //Return user(s)
        $returnArray = $this->db->gatherUsers();


        //User is in db
        $this->assertTrue($returnArray[0]['email'] == $email);
    }

    public function testNewVideo()
    {
        $name = "name namesen";
        $email = "test@test.test";
        $password = "something";

        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $name, 'email' => $email, 'password' => $password]);
    

        $userid = "1";
        $title = "testvideo";
        
        $result = $this->db->newVideo($userid,$title,"desc","topic","course","nothing","nothing");

        $this->assertTrue($result);

        $this->tester->seeInDatabase('video', ['userid' => $userid, 'title' => $title]);

    }

    public function testReturnVideos()
    {
        $name = "name namesen";
        $email = "test@test.test";
        $password = "something";

        $userid = "1";
        $title = "testvideo";

        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $name, 'email' => $email, 'password' => $password]);

        $this->tester->haveInDatabase('video',['userid' => $userid, 'title' => $title]);

        $returnArray = $this->db->returnVideos($userid);

        $this->assertTrue($returnArray[0]['title'] == $title);
    }

    public function testReturnVideo()
    {

        $name = "name namesen";
        $email = "test@test.test";
        $password = "something";

        $userid = "1";
        $title = "testvideo";

        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $name, 'email' => $email, 'password' => $password]);
        
        $this->tester->haveInDatabase('video',['userid' => $userid, 'title' => $title]);

        $returnArray = $this->db->returnVideo("1");

        $this->assertTrue($returnArray[0]['title'] == $title);

    }

    public function testReturnAllVideos()
    {
        $name = "name namesen";
        $email = "test@test.test";
        $password = "something";

        $userid = "1";
        $title = "testvideo";

        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $name, 'email' => $email, 'password' => $password]);

        $this->tester->haveInDatabase('video',['userid' => $userid, 'title' => $title]);

        $returnArray = $this->db->returnAllVideos();

        $this->assertTrue($returnArray[0]['title'] == $title);
    }

    public function testDeleteVideo() 
    {
        $name = "name namesen";
        $email = "test@test.test";
        $password = "something";

        $userid = "1";
        $title = "testvideo";

        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $name, 'email' => $email, 'password' => $password]);

        $this->tester->haveInDatabase('video',['userid' => $userid, 'title' => $title]);

        $this->tester->seeInDatabase('video',['userid' => $userid, 'title' => $title]);

        $this->db->deleteVideo("1");

        $this->tester->dontSeeInDatabase('video',['userid' => $userid, 'title' => $title]);

    }

    public function testUpdateVideo()
    {
        $name = "name namesen";
        $email = "test@test.test";
        $password = "something";

        $userid = "1";
        $title = "testvideo";
        $desc = "desc";
        $topic = "topic";
        $course = "course";

        //Add a user inorder for the relation to be correct
        $this->tester->haveInDatabase('users',['name' => $name, 'email' => $email, 'password' => $password]);

        $this->tester->haveInDatabase('video',['userid' => $userid, 'title' => $title, 'description' => $desc, 'topic' => $topic, 'course' => $course]);

        $this->db->updateVideo("1","newTitle","newDesc","newTopic","newCourse");

        $this->tester->seeInDatabase('video',['userid' => $userid, 'title' => "newTitle", 'description' => "newDesc", 'topic' => "newTopic", 'course' => "newCourse"]);

    }

}