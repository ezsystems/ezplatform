<?php


namespace App\Page;

use Behat\Mink\Session;
use EzSystems\Behat\Test\PageObject\Page;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;

class DashboardPage extends Page
{
    protected $fields = ['meSection' => '.card-body'];

    protected $route = '/admin/dashboard';

    /**
     * @var string
     */
    private $rootContentTypeName;

    public function __construct(Session $session, MinkParameters $minkParameters, string $rootContentTypeName)
    {
        parent::__construct($session, $minkParameters);
        $this->rootContentTypeName = $rootContentTypeName;
    }

    function verifyIsLoaded(): void
    {
        var_dump($this->rootContentTypeName);
    }

    public function getName(): string
    {
        return 'Dashboard';
    }
}