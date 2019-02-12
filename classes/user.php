<?php
require_once 'db.php';

class User
{

    private $username;
    private $email;
    private $privileges;

    private $db = null;
    private $dbh = null;

    public function __construct($m_username) {
        //Grab userinfo from db
        //Initalize a new database connection
        $db = new DB();

        //Get an instance of the current connection
        $dbh = $db->getDBConnection();

        //Find user in DB and store info in class variables
        $userArray = $db->findUser($m_username);
        $this->username = $userArray['username'];
        $this->email = $userArray['email'];
        $this->privileges = $userArray['privileges'];

    }

   function __destruct() {

    }

    public function returnEmail(){ //Returns users email
        return $this->email;
    }

    public function setPrivileges($m_privileges){ //Updates privileges INCOMPLETE
        //Try to update DB record first
        $this->privileges = $m_privileges;
    }

    public function getPrivileges(){ //Get users privileges
        return $this->privileges;
    }

}


?>