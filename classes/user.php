<?php
require_once 'db.php';

class User
{

    private $id;
    private $email;
    private $privileges;

    private $db = null;

    public function __construct($m_email) {
        //Grab userinfo from db
        //Initalize a new database connection
        $db = new DB();

        //Find user in DB and store info in class variables
        $userArray = $db->findUser($m_email);
        $this->id = $userArray['id'];
        $this->email = $userArray['email'];
        $this->privileges = $userArray['privileges'];

    }

   function __destruct() {

    }

    public function returnEmail(){ //Returns users email
        return $this->email;
    }

    public function getPrivileges(){ //Get users privileges
        return $this->privileges;
    }

    public function returnId(){
        return $this->id;
    }

}


?>