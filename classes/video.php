<?php

class Video
{

    public $videoid;
    public $title;
    public $description;
    public $topic;
    public $course;
    




    function __construct($videoid, $title, $description, $topic, $course) {
        $this->videoid = $videoid;
        $this->title = $title;
        $this->description = $description;
        $this->topic = $topic;
        $this->course = $course;

        
    }

    /*function __destruct() {

    }
    */
}


?>