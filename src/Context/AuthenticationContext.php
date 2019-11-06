<?php

namespace App\Context;

use App\Page\LoginPage;
use Behat\Behat\Context\Context;

class AuthenticationContext implements Context
{
    /**
     * @var LoginPage
     */
    private $loginPage;

    public function __construct(LoginPage $loginPage)
    {
        $this->loginPage = $loginPage;
    }

    /**
     * @When I log in as :username with password :password
     */
    public function iLoginAs(string $username, string $password): void
    {
        $this->loginPage->verifyIsLoaded();
        $this->loginPage->login($username, $password);
    }

    /**
     * @Given I am logged as :username
     */
    public function iAmLoggedAs(string $username): void
    {
        $this->loginPage->verifyIsLoaded();
//        $loginPage->setParameters('blabla')
        $this->loginPage->open();
        $this->loginPage->login($username, 'publish');
    }
}
