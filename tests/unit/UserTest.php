<?php 

require_once './classes/user.php';
require_once './classes/db.php';

class UserTest extends \Codeception\Test\Unit
{

    //Test related variables

    //User
    private $name = "name namesen";
    private $email = "test@test.test";
    private $password = "something";

    private $db = null;
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
        //Register a user
        $this->db = new DB();
        $this->db->registerUser($this->name , $this->email, $this->password, true);
        
    }

    protected function _after()
    {
    }

    // tests

    public function testReturnEmail()
    {
        $test_email = "test@test.test";

        $user = new User($test_email);
        $email = $user->returnEmail();

        $this->assertEquals($this->email, $test_email);
    }

    public function testGetPrivileges(){

        $test_privileges = "0";

        $user = new User($this->email);
        $privileges = $user->getPrivileges();

        $this->assertEquals($privileges, $test_privileges);
    }

    public function testUpdateUser(){
        $oldEmail = "test@test.test";

        $newEmail = "newtest@newtest.newtest";
        $test_id = "1";

        $user = new User($oldEmail);
        $mail = $user->returnEmail();

        $user->updateUser($test_id, "", $newEmail, "" , "");

        $user = new User($newEmail);
        $this->assertTrue($newEmail == $user->returnEmail());

    }

    public function testReturnId(){
        $test_id = "1";

        $user = new User($this->email);
        $id = $user->returnId();

        $this->assertEquals($id, $test_id);
    }

    public function testReturnPicture(){
        $test_picture = "https://propertymarketersllc.com/wp-content/uploads/2018/05/profile-picture-placeholder.png";
        $this->assertTrue($this->user->returnPicture() == $test_picture);
    }
    
    public function testReturnName(){
        $this->assertTrue($this->user->returnName() == $this->name);
    }

}