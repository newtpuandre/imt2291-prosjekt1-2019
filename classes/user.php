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

    public function returnEmail(){
        return $this->email;
    }

    public function setPrivileges($m_privileges){
        $this->privileges = $m_privileges;
    }

}


?>