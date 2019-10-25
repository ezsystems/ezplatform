<?php
declare(strict_types=1);

namespace App\Page;

use EzSystems\Behat\Test\PageObject\Page;

class LoginPage extends Page
{
    protected $route = '/admin/login';

    protected $fields = [
        'username' => ['css', '#username'],
        'password' => ['css', '#password'],
        'loginForm' => ['css', '.ez-login__form-wrapper'],
    ];

    /**
     * Performs login action.
     *
     * @param string $username
     * @param string $password
     */
    public function login(string $username, string $password): void
    {
        $this->fillUsername($username);
        $this->fillPassword($password);
        $this->clickLogin();
    }

    /**
     * Clicks login button.
     */
    protected function clickLogin(): void
    {
        $this->getHTMLPage()->find(...$this->fields['loginForm'])->findButton('Login')->click();
    }

    /**
     * Fills username field.
     *
     * @param string $username
     */
    protected function fillUsername(string $username): void
    {
        $this->getHTMLPage()->find(...$this->fields['loginForm'])->find(...$this->fields['username'])->setValue($username);
    }

    /**
     * Fills password field.
     *
     * @param string $password
     */
    protected function fillPassword(string $password): void
    {
        $this->getHTMLPage()->find(...$this->fields['password'])->setValue($password);
    }

    function verifyIsLoaded(): void
    {
        $this->getWebAssert()->elementExists(...$this->fields['username']);
        $this->getWebAssert()->elementExists(...$this->fields['password']);
    }

    public function getName(): string
    {
        return 'Login';
    }
}
