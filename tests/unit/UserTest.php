<?php 

require_once './classes/user.php';
require_once './classes/db.php';

class UserTest extends \Codeception\Test\Unit
{
    private $db = null;
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
        //Register a user

        $email = "test@test.test";
        $password = "something";


        $this->db = new DB();
        $this->db->registerUser($email, $password, true);
        
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

        $this->assertEquals($email, $test_email);
    }

    public function testGetPrivileges(){
        $email = "test@test.test";
        $test_privileges = "0";

        $user = new User($email);
        $privileges = $user->getPrivileges();

        $this->assertEquals($privileges, $test_privileges);
    }

    public function testGetId(){
        $email = "test@test.test";
        $test_id = "1";

        $user = new User($email);
        $id = $user->returnId();

        $this->assertEquals($id, $test_id);
    }

    public function testUserLogin(){
        $email = "test@test.test";
        $password = "something";

        $this->assertTrue($this->db->loginUser($email, $password));
    }

}