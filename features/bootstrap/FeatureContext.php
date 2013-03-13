<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use PHPUnit_Framework_Assert as Assertion;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
    protected $pageIdentifierMap = array(
        'Search Page' => '/content/search',
    );

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
    }

    /**
     * @When /^I search for "([^"]*)"$/
     */
    public function iSearchFor($searchPhrase)
    {
        $searchField = $this->getSession()->getPage()->findById('site-wide-search-field');

        Assertion::assertNotNull($searchField, 'Search field not found.');

        $searchField->setValue($searchPhrase);
        $searchField->keyPress(13);
        // $this->getSession()->executeScript("$('#site-wide-search').submit();");
    }

    /**
     * @Then /^I am on the "([^"]*)"$/
     */
    public function iAmOnThe($pageIdentifier)
    {
        // FIXME: Replace with waiting for the page to load
        $this->getSession()->wait(5000);

        $currentUrl = $this->getUrlWithoutQueryString($this->getSession()->getCurrentUrl());

        $expectedUrl = $this->locatePath($this->getPathByPageIdentifier($pageIdentifier));

        Assertion::assertEquals(
            $expectedUrl,
            $currentUrl,
            "Unexpected URL of the current site."
        );
    }

    /**
     * Returns the path associated with $pageIdentifier
     *
     * @param string $pageIdentifier
     * @return string
     */
    protected function getPathByPageIdentifier($pageIdentifier)
    {
        if (!isset($this->pageIdentifierMap[$pageIdentifier])) {
            throw new \RuntimeException("Unknown page identifier '{$pageIdentifier}'.");
        }
        return $this->pageIdentifierMap[$pageIdentifier];
    }

    /**
     * Returns $url without its query string
     *
     * @param string $url
     * @return string
     */
    protected function getUrlWithoutQueryString($url)
    {
        if (strpos($url, '?') !== false) {
            $url = substr($url, 0, strpos($url, '?'));
        }
        return $url;
    }

    /**
     * @Given /^I see search (\d+) result$/
     */
    public function iSeeSearchResults($arg1)
    {
        $resultCountElement = $this->getSession()->getPage()->find('css', 'div.feedback');

        Assertion::assertNotNull(
            $resultCountElement,
            'Could not find result count text element.'
        );

        $resultText = $resultCountElement->getText();

        Assertion::assertEquals(
            'Search for "welcome" returned 1 matches',
            $resultText
        );
    }


//
// Place your definition and hook methods here:
//
//    /**
//     * @Given /^I have done something with "([^"]*)"$/
//     */
//    public function iHaveDoneSomethingWith($argument)
//    {
//        doSomethingWith($argument);
//    }
//
}
