<?php 

class PlaylistCest
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

    }

    public function CreatePlaylist (FunctionalTester $I)
    {
        $I->amOnPage('editPlaylist.php');
        $I->see('Mine spillelister');
        
        $I->click("Opprett ny spilleliste");
        $I->seeCurrentUrlMatches('~createNew~');
        $I->fillField('name','test_func');
        $I->fillField('description', 'test_func_desc');
        $I->attachFile('thumbnail_file', 'test_picture.jpg');
        $I->click('Lag spilleliste');

        $I->dontSee('Noe gikk galt!');
        $I->seeCurrentUrlMatches('~editPlaylist.php~');
        $I->see('test_func');
        $I->see('test_func_desc');
    }

    public function addVideoToPlaylist (FunctionalTester $I)
    {
        //Add three videos

        $I->amOnPage('editPlaylist.php');
        $I->see('Mine spillelister');

        $I->click('Endre');
        $I->see('Endre spilleliste');

        $I->click('Legg til videoer i spilleliste');

        $I->checkOption('#checkbox1');
        $I->checkOption('#checkbox2');
        $I->checkOption('#checkbox3');

        $I->click('Oppdater spilleliste');
        $I->dontSee('Noe gikk galt!');

    }

    public function changePosition (FunctionalTester $I)
    {
        $I->amOnPage('editPlaylist.php');
        $I->see('Mine spillelister');

        $I->click('Endre');
        $I->see('Endre spilleliste');

        $I->click('body > main > div:nth-child(7) > a:nth-child(4)'); //Change position of the middle element

        $I->dontSee('Noe gikk galt!');

    }

}
