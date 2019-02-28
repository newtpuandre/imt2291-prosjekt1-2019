<?php 

require_once 'classes/admin.php';

class CreateUserCest
{
    private $admin;

    public function RegisterUser(FunctionalTester $I)
    {
        //Log the user in
        $I->amOnPage('/register.php');
        $I->seeCurrentUrlEquals('/imt2291-prosjekt1-2019/register.php');
        $I->fillField('name', 'Test Testesen'); //Change to something that exists
        $I->fillField('email', 'test@test.test'); //Change to something that exists
        $I->fillField('password', 'test123'); //Change to something that exists
        $I->click('Registrer meg');
        $I->seeCurrentUrlEquals('/imt2291-prosjekt1-2019/register.php?status=ok');

        $this->admin = new Admin();
        $this->admin->updatePrivileges("test@test.test", "2"); //Give user the correct privileges        
    }
}