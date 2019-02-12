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

    public function findUser($m_username){ //m_username can also be password.
        $sql = 'SELECT id, username, email, privileges FROM users WHERE username=:username OR email=:username';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':username', $m_username);
        $sth->execute();
        if ($row = $sth->fetch()) {
            return $row;
            
        } else {
            return null;
        }
    }

    public function registerUser($m_username,$m_email,$m_password) {

        $sql = 'INSERT INTO users (username, email , password) values (?, ?, ?)';
        $sth = $this->dbh->prepare($sql);
        // Use password_hash to encrypt password : http://php.net/manual/en/function.password-hash.php
        $sth->execute (array ($m_username, $m_email,
                          password_hash($m_password, PASSWORD_DEFAULT)));
        if ($sth->rowCount()==1) {
            return true;
        } else {
            return false;
        }
    }

    public function loginUser($m_username,$m_password){

        $sql = 'SELECT password, id FROM users WHERE username=:username OR email=:username';
        $sth = $this->dbh->prepare ($sql);
        $sth->bindParam(':username', $_POST['username']);
        $sth->execute();
        if ($row = $sth->fetch()) { // get id and hashed password for given user
            // Use password_verify to check given password : http://php.net/manual/en/function.password-verify.php
            if (password_verify($_POST['password'], $row['password'])) {
                return true;
    
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}



?>