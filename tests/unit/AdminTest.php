<?php

require_once './classes/admin.php';
require_once './classes/db.php';

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
        $name = "name namesen";
        $email = "test@test.test";
        $password = "something";


        $this->db = new DB();
        $this->db->registerUser($name, $email, $password, true);
        
    }

    protected function _after()
    {
    }

    public function testGatherUsers()
    {
        $admin = new Admin();
        $users = $admin->gatherUsers();

        $this->assertTrue($users[0]['email'] == "test@test.test");
    }

    public function testUpdatePrivileges()
    {
        $admin = new Admin();
        $admin->updatePrivileges("test@test.test", "2");

        $this->tester->seeInDatabase('users', ['email' => 'test@test.test', 'privileges' => '2']);
    }

    public function testCountIAmTeacher()
    {
        $count = $this->db->countIAmTeacher();

        $this->assertTrue($count['num'] > 0);
    }

    public function testRemoveIAmTeacher()
    {
        $admin = new Admin();
        $admin->removeIAmTeacher("1");

        $this->tester->seeInDatabase('users', ['email' => 'test@test.test', 'isTeacher' => '0']);

    }

}    
?>