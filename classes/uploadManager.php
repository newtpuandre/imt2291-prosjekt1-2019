<?php

require_once 'DB.php';
require_once 'Video.php';

//to deal with managing the upload content, sending it to the DB

class uploadManager {

    private $db;


    public function __construct($db) {
        $this->db = $db;
    }

    function upload($title, $description, $course, $topic){

        //todo


    }
}