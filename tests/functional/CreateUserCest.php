<?php 

require_once 'classes/admin.php';

class CreateUserCest
{
    private $admin;

    public function RegisterUser(FunctionalTester $I)
    {
        //Log the user in
        $I->amOnPage('/register.php');
        //$I->seeCurrentUrlContains('/register.php');
        $I->seeCurrentUrlMatches('~register.php~');
        $I->fillField('name', 'Test Testesen'); //Change to something that exists
        $I->fillField('email', 'test@test.test'); //Change to something that exists
        $I->fillField('password', 'test123'); //Change to something that exists
        $I->click('Registrer meg');
        $I->seeCurrentUrlMatches('~status=ok~');

        $this->admin = new Admin();
        $this->admin->updatePrivileges("test@test.test", "2"); //Give user the correct privileges        
    }
}