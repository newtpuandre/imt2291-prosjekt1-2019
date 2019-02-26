<?php 

require_once './classes/db.php';
require_once './classes/video.php';

class VideoTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()    
    {
          //Create db connection
          $this->db = new DB();
    }

    protected function _after()
    {
    }

    // tests
    

    public function testUploadVideo()
    {

    }

    public function testUpdateVideo()
    {
        
    }

    public function testGetAllUserVideos()
    {
        
    }

    public function testGetAllVideosWithLecturers()
    {
        
    }

    public function testGetVideo()
    {
        
    }

    public function testGetVideoLecturer()
    {
        
    }

    public function testDeleteVideo()
    {
        
    }

    public function testThumbnailResize()
    {
        
    }

    public function testSearchForVideo()
    {
        
    }

    public function testGetAllVideoCourses()
    {
        
    }

    public function testGetNewVideos()
    {
        
    }

    public function testSearchVideoCourse()
    {
        
    }
}