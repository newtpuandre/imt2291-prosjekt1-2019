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


    }

}