<?php
require_once 'db.php';

class User
{

    private $id;
    private $email;
    private $privileges;
    private $name;
    private $picture;

    private $db = null;

    public function __construct($m_email) {
        //Initalize a new database connection
        $this->db = new DB();

        //Find user in DB and store info in class variables
        $userArray = $this->db->findUser($m_email);
        $this->id = $userArray['id'];
        $this->email = $userArray['email'];
        $this->privileges = $userArray['privileges'];
        $this->name = $userArray['name'];
        $this->picture = $userArray['picture'];
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

    public function returnName(){
        return $this->name;
    }

    public function returnPicture(){
        return $this->picture;
    }

}


?>