<?php


class DB
{
    //Edit the variables below to the correct database settings
    private static $db=null;
    private $dsn = 'mysql:dbname=prosjekt1;host=127.0.0.1'; 
    private $user = 'root';
    private $password = '';
    private $dbh = null;

    function __construct() {
        //Attempt connection to the database

        try {
            $this->dbh = new PDO($this->dsn, $this->user, $this->password);
        } catch (PDOException $e) {
            // NOTE IKKE BRUK DETTE I PRODUKSJON
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    function __destruct() {
       
    }

    public function getDBConnection() {
        return $this->dbh;
    }

    public function findUser($m_email){
        $sql = 'SELECT id, email, privileges FROM users WHERE email=:email';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':email', $m_email);
        $sth->execute();
        if ($row = $sth->fetch()) {
            return $row;
            
        } else {
            return null;
        }
    }

    public function registerUser($m_email,$m_password, $m_isTeacher) {

        $sql = 'INSERT INTO users (email , password, isTeacher) values (?, ?, ?)';
        $sth = $this->dbh->prepare($sql);
        // Use password_hash to encrypt password : http://php.net/manual/en/function.password-hash.php
        $sth->execute (array ($m_email,
                          password_hash($m_password, PASSWORD_DEFAULT),$m_isTeacher));
        if ($sth->rowCount()==1) {
            return true;
        } else {
            return false;
        }
    }

    public function loginUser($m_email,$m_password){

        $sql = 'SELECT password, id FROM users WHERE email=:email';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':email', $m_email);
        $sth->execute();
        if ($row = $sth->fetch()) { // get id and hashed password for given user
            // Use password_verify to check given password : http://php.net/manual/en/function.password-verify.php
            if (password_verify($m_password, $row['password'])) {
                return true;
    
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function gatherUsers(){
        $sql = 'SELECT email, privileges, isTeacher FROM users ORDER BY id DESC ';
        $sth = $this->dbh->prepare ($sql);
        $sth->execute();
        if ($row = $sth->fetchAll()) {
            return $row;
            
        } else {
            return null;
        }
    }

    public function updatePrivileges($m_email, $m_privilevel) {
        $sql = 'UPDATE users SET PRIVILEGES = :privileges WHERE email=:email';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':privileges',$m_privilevel);
        $sth->bindParam(':email', $m_email);
        $sth->execute();
        if ($row = $sth->fetch()) {

        } else {
            return false;
        }
    }

}



?>