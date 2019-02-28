<?php 

class VideoCest
{
    
    public function _before(FunctionalTester $I)
    {
        //Log the user in
        $I->amOnPage('/login.php');
        $I->seeCurrentUrlMatches('~login.php~');
        $I->fillField('email', 'test@test.test'); //Change to something that exists
        $I->fillField('password', 'test123'); //Change to something that exists
        $I->click('Log inn');
        $I->seeCurrentUrlMatches('~index.php~');

        $I->dontSee('Noe gikk galt!');
    }

    //This functions uploads 3 videos. I couldnt find a more elegant way to do this...
    public function uploadVideo(FunctionalTester $I)
    {
            $I->amOnPage('videoUpload.php');
            $I->seeCurrentUrlMatches('~videoUpload.php~');
            $I->fillField('upload_title','test_video');
            $I->fillField('upload_desc','test_video');
            $I->fillField('upload_course', 'test_course');
            $I->fillField('upload_topic', 'test_topic');
            $I->attachFile('upload_video', 'test_video.mp4');
            $I->attachFile('upload_thumbnail', 'test_picture.jpg');
            $I->click('upload_btn');
            $I->dontSee('Noe gikk galt!');

            $I->amOnPage('videoUpload.php');
            $I->seeCurrentUrlMatches('~videoUpload.php~');
            $I->fillField('upload_title','test_video');
            $I->fillField('upload_desc','test_video');
            $I->fillField('upload_course', 'test_course');
            $I->fillField('upload_topic', 'test_topic');
            $I->attachFile('upload_video', 'test_video.mp4');
            $I->attachFile('upload_thumbnail', 'test_picture.jpg');
            $I->click('upload_btn');
            $I->dontSee('Noe gikk galt!');

            $I->amOnPage('videoUpload.php');
            $I->seeCurrentUrlMatches('~videoUpload.php~');
            $I->fillField('upload_title','test_video');
            $I->fillField('upload_desc','test_video');
            $I->fillField('upload_course', 'test_course');
            $I->fillField('upload_topic', 'test_topic');
            $I->attachFile('upload_video', 'test_video.mp4');
            $I->attachFile('upload_thumbnail', 'test_picture.jpg');
            $I->click('upload_btn');
            $I->dontSee('Noe gikk galt!');
    }

}