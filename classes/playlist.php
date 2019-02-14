<?php

require_once 'db.php';


class Playlist
{

    private $title;
    private $description;


    function __construct($title, $description) {
        $this->title = $title;
        $this->description = $description;
    }

   /* function __destruct() {

    }

    */

    public function returnTitle(){
        return $this->title;
    }

    public function returnDescription(){
        return $this->description;
    }


}


?>