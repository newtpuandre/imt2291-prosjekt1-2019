<?php 
use \Codeception\Util\Locator;
class PlaylistCest
{

    public function _before(FunctionalTester $I)
    {
        //Log the user in
        $I->amOnPage('/login.php');
        $I->seeCurrentUrlEquals('/imt2291-prosjekt1-2019/login.php');
        $I->fillField('email', 'something'); //Change to something that exists
        $I->fillField('password', 'something'); //Change to something that exists
        $I->click('Log inn');
        $I->seeCurrentUrlEquals('/imt2291-prosjekt1-2019/index.php');
    }

    public function CreatePlaylist (FunctionalTester $I)
    {
        $I->amOnPage('/imt2291-prosjekt1-2019/playlist.php');
        $I->see('Mine spillelister');
        

        $I->click("create");

        $I->seeCurrentUrlEquals('/imt2291-prosjekt1-2019/editPlaylist.php?createNew');
    }
}
