<?php

require_once './classes/admin.php';

class AdminTest extends \Codeception\Test\Unit
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

    public function testGatherUsers(){
        $admin = new Admin();
        $users = $admin->gatherUsers();

        $this->assertTrue($users[0]['email'] == "test@test.test");
    }

    public function testUpdatePrivileges(){
        $admin = new Admin();
        $admin->updatePrivileges("test@test.test", "2");

        $this->tester->seeInDatabase('users', ['email' => 'test@test.test', 'privileges' => '2']);
    }

}    
?>