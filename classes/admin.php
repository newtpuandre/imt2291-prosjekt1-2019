<?php

require_once 'db.php';

class Admin
{

    private $db = null;
    private $dbh = null;


    function __construct() {
        $this->db = new DB();

        $this->dbh = $this->db->getDBConnection();
    }

    function __destruct() {

    }

    public function gatherUsers(){ //Returns an array of all users
        return $this->db->gatherUsers();
    }

    public function updatePrivileges($m_email, $m_privlevel){ //Updates a specific users privilege
        if($this->db->updatePrivileges($m_email,$m_privlevel)) {
            return true;
        } else {
            return false;
        }
    }

    public function countIAmTeacher(){
        return $this->db->countIAmTeacher();
    }

    public function removeIAmTeacher($m_id){
        return $this->db->removeIAmTeacher($m_id);
    }
}

?>