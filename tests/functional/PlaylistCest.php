<?php 

class PlaylistCest
{
    

    public function _before(FunctionalTester $I)
    {
        //Log the user in
        $I->amOnPage('/login.php');
        $I->seeCurrentUrlEquals('/imt2291-prosjekt1-2019/login.php');
        $I->fillField('email', 'test@test.test'); //Change to something that exists
        $I->fillField('password', 'test123'); //Change to something that exists
        $I->click('Log inn');
        $I->seeCurrentUrlEquals('/imt2291-prosjekt1-2019/index.php');
    }

    public function CreatePlaylist (FunctionalTester $I)
    {
        $I->amOnPage('/imt2291-prosjekt1-2019/editPlaylist.php');
        $I->see('Mine spillelister');
        
        $I->click("Opprett ny spilleliste");
        $I->seeCurrentUrlEquals('/imt2291-prosjekt1-2019/editPlaylist.php?createNew');
        $I->fillField('name','test_func');
        $I->fillField('description', 'test_func_desc');
        $I->attachFile('thumbnail_file', 'test_picture.jpg');
        $I->click('Lag spilleliste');

        $I->seeCurrentUrlEquals('/imt2291-prosjekt1-2019/editPlaylist.php');
        $I->see('test_func');
        $I->see('test_func_desc');
    }

    public function AddVideoToPlaylist (FunctionalTester $I)
    {
        $I->amOnPage('/imt2291-prosjekt1-2019/editPlaylist.php');
        $I->see('Mine spillelister');

        $I->click('Endre');
        $I->see('Endre spilleliste');

        $I->click('Legg til videoer i spilleliste');

        $I->checkOption('videoids[]');

        $I->click('Oppdater spilleliste');

    }

}
