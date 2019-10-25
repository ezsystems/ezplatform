<?php

namespace App\Context;

use App\Page\DashboardPage;
use Behat\Behat\Context\Context;
use EzSystems\Behat\Test\Factory\ElementFactoryInterface;
use EzSystems\Behat\Test\Factory\PageObjectFactoryInterface;

class NavigationContext implements Context
{
    /**
     * @var PageObjectFactoryInterface
     */
    private $pageObjectFactory;

    public function __construct(PageObjectFactoryInterface $pageObjectFactory)
    {
        $this->pageObjectFactory = $pageObjectFactory;
    }

    /**
     * @Given I open :pageName page
     */
    public function openPage($pageName)
    {
        $page = $this->pageObjectFactory->create($pageName);
        $page->open();
    }
    /**
     * @Given I try to open :pageName page
     */
    public function tryToOpenPage($pageName)
    {
        $page = $this->pageObjectFactory->create($pageName);
        $page->tryToOpen();
    }
    /**
     * @Then I should be on :pageName page
     */
    public function iAmOnPage($pageName)
    {
        $page = $this->pageObjectFactory->create($pageName);
        $page->verifyIsLoaded();
    }
}