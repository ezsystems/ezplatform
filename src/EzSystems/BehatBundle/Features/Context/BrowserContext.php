<?php
/**
 * File containing the BrowserContext class.
 *
 * This class contains general feature context for Behat.
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Features\Context;

use EzSystems\BehatBundle\Features\Context\FeatureContext as BaseFeatureContext;
use PHPUnit_Framework_Assert as Assertion;
use Behat\Behat\Context\Step;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Exception\PendingException;
use Behat\Mink\Exception\UnsupportedDriverActionException as MinkUnsupportedDriverActionException;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\Driver\GoutteDriver;

/**
 * Browser interface helper context.
 */
class BrowserContext extends BaseFeatureContext
{
    /**
     * This will tell us which containers (design) to search, should be set by child classes.
     *
     * ex:
     * $mainAttributes = array(
     *      "content"   => "thisIsATag",
     *      "column"    => array( "class" => "thisIstheClassOfTheColumns" ),
     *      "menu"      => "//xpath/for/the[menu]",
     *      ...
     * );
     *
     * Is possible to define the specific xpath for a block, and all the other
     * options won't be processed, however this should ONLY be used when testing
     * content, otherwise if something changes on block it won't work
     *
     * @var array This will have a ( identifier => array )
     */
    protected $mainAttributes = array();

    /**
     * @var string
     */
    protected $priorSearchPhrase = '';

    public function __construct( array $parameters )
    {
        parent::__construct( $parameters );

        // add home to the page identifiers
        $this->pageIdentifierMap += array( "home" => "/" );
    }

    /**
     * This method works as a complement to the $mainAttributes var
     *
     * @param  string $block This should be an identifier for the block to use
     *
     * @return string
     *
     * @see $this->mainAttributes
     */
    protected function makeXpathForBlock( $block = 'main' )
    {
        $parameter = ( isset( $this->mainAttributes[strtolower( $block )] ) ) ?
            $this->mainAttributes[strtolower( $block )] :
            NULL;

        Assertion::assertNotNull( $parameter, "Element {$block} is not defined" );

        $xpath = $this->mainAttributes[strtolower( $block )];
        // check if value is a composed array
        if ( is_array( $xpath ) )
        {
            // if there is an xpath defined look no more!
            if ( isset( $xpath['xpath'] ) )
            {
                return $xpath['xpath'];
            }

            $nuXpath = "";
            // verify if there is a tag
            if ( isset( $xpath['tag'] ) )
            {
                if ( strpos( $xpath['tag'], "/" ) === 0 || strpos( $xpath['tag'], "(" ) === 0 )
                {
                    $nuXpath = $xpath['tag'];
                }
                else
                {
                    $nuXpath = "//" . $xpath['tag'];
                }

                unset( $xpath['tag'] );
            }
            else
            {
                $nuXpath = "//*";
            }

            foreach ( $xpath as $key => $value )
            {
                switch ( $key )
                {
                    case "text":
                        $att = "text()";
                        break;
                    default:
                        $att = "@$key";
                }
                $nuXpath .= "[contains($att, {$this->literal( $value )})]";
            }

            return $nuXpath;
        }

        //  if the string is an Xpath
        if ( strpos( $xpath, "/" ) === 0 || strpos( $xpath, "(" ) === 0 )
        {
            return $xpath;
        }

        // if xpath is an simple tag
        return "//$xpath";
    }

    /**
     * With this function we get a centralized way to define what are the possible
     * tags for a type of data and return them as a xpath search
     *
     * @param  string $type Type of text (ie: if header/title, or list element, ...)
     *
     * @return string Xpath string for searching elements inside those tags
     *
     * @throws PendingException If the $type isn't defined yet
     */
    protected function getTagsFor( $type )
    {
        switch ( strtolower( $type ) )
        {
            case "topic":
            case "header":
            case "title":
                return array( "h1", "h2", "h3" );
            case "list":
                return array( "li" );
        }

        throw new PendingException( "Tag's for '$type' type not defined" );
    }

    /**
     * This should be seen as a complement to self::getTagsFor() where it will
     * get the respective tags from there and will make a valid Xpath string with
     * all OR's needed
     *
     * @param array  $tags  Array of tags strings (ex: array( "a", "p", "h3", "table" ) )
     * @param string $xpath String to be concatenated to each tag
     *
     * @return string
     */
    protected function concatTagsWithXpath( array $tags, $xpath = null )
    {
        $finalXpath = "";
        for ( $i = 0; !empty( $tags[$i] ); $i++ )
        {
            $finalXpath .= "//{$tags[$i]}$xpath";
            if ( !empty( $tags[$i + 1] ) )
            {
                $finalXpath .= " | ";
            }
        }

        return $finalXpath;
    }

    /**
     * This is a simple shortcut for
     * $this->getSession()->getPage()->getSelectorsHandler()->xpathLiteral()
     *
     * @param string $text
     */
    protected function literal( $text )
    {
        return $this->getSession()->getSelectorsHandler()->xpathLiteral( $text );
    }

    /**
     * This function is used for testing if the driver supports redirect interception
     * for the "I follow the redirection" step
     *
     * @throws UnsupportedDriverActionException
     */
    protected function canIntercept()
    {
        $driver = $this->getSession()->getDriver();
        if ( !$driver instanceof GoutteDriver )
        {
            throw new UnsupportedDriverActionException(
                'You need to tag the scenario with ' .
                '"@mink:goutte" or "@mink:symfony". ' .
                'Intercepting the redirections is not ' .
                'supported by %s', $driver
            );
        }
    }

    /**
     * @When /^I follow the redirection$/
     * @Then /^I should be redirected$/
     */
    public function iFollowTheRedirection()
    {
        $this->canIntercept();
        $client = $this->getSession()->getDriver()->getClient();
        $client->followRedirects( true );
        $client->followRedirect();
    }

    /**
     * @Given /^I am not logged in$/
     */
    public function iAmNotLoggedIn()
    {
        return array(
            new Step\Given( 'I am on "/user/logout"' ),
            new Step\Then( 'I see "Homepage" page' ),
        );
    }

    /**
     * @Given /^(?:|I )am logged in as "([^"]*)" with password "([^"]*)"$/
     * @Given /^(?:|I )am logged in as "([^"]*)"$/
     */
    public function iAmLoggedInAsWithPassword( $user, $password = NULL )
    {
        if ( !isset( $password ) )
        {
            return array(
                new Step\Given( 'I am on "/logout"' ),
                new Step\Then( 'I should be on "/"' ),
            );
        }
        else
        {
            return array(
                new Step\Given( 'I am on "/user/login"' ), // @todo /user/login needs to be updated to /login
                new Step\When( 'I fill in "Username" with "' . $user . '"' ),
                new Step\When( 'I fill in "Password" with "' . $password . '"' ),
                new Step\When( 'I press "Login"' ),
                new Step\Then( 'I should be on "/"' ),
            );
        }
    }

    /**
     * @Given /^I am logged in as an "([^"]*)"$/
     */
    public function iAmLoggedInAsAn( $role )
    {
        switch ( $role )
        {
            case 'administrator':
                return new Step\Given( "I am logged in as \"Admin\" with password \"publish\"" );
            default:
                throw new PendingException( "Role {$role} does not exists" );
        }
    }

    /**
     * @Then /^(?:|I )see "([^"]*)" page$/
     *
     */
    public function iSeePage( $pageIdentifier )
    {
        $currentUrl = $this->getUrlWithoutQueryString( $this->getSession()->getCurrentUrl() );

        $expectedUrl = $this->locatePath( $this->getPathByPageIdentifier( $pageIdentifier ) );

        Assertion::assertEquals(
            $expectedUrl,
            $currentUrl,
            "Unexpected URL of the current site. Expected: '$expectedUrl'. Actual: '$currentUrl'."
        );
    }

    /**
     * @When /^(?:|I )click (?:on|at) (?:the |)"([^"]*)" button$/
     *
     * Can also be used @Given steps
     */
    public function iClickAtButton( $button )
    {
        return array(
            new Step\When( "I press \"{$button}\"" )
        );
    }

    /**
     * @When /^(?:|I )click (?:on|at) (?:the |)"([^"]*)" link$/
     *
     * Can also be used @Given steps
     */
    public function iClickAtLink( $link )
    {
        return array(
            new Step\When( "I follow \"{$link}\"" )
        );
    }

    /**
     * @When /^on "([A-Za-z\s]*)" I click at "([^"]*)" link$/
     */
    public function onSomePlaceIClickAtLink( $somePlace, $link )
    {
        $base = $this->makeXpathForBlock( $somePlace );

        $literal = $this->literal( $link );
        $el = $this->getSession()->getPage()->find(
            "xpath",
            "$base//a[@href][contains( text(), $literal ) or contains( @href, $literal )]"
        );

        Assertion::assertNotNull( $el, "Couldn't find '$link' link" );

        $el->click();
    }

    /**
     * @When /^on "([^"]*)" I click (?:at |on |)"([^"]*)" button$/
     */
    public function onSomePlaceIClickAtButton( $somePlace, $button )
    {
        $base = $this->makeXpathForBlock( $somePlace );

        $literal = $this->literal( $button );
        $el = $this->getSession()->getPage()->find(
            "xpath", "$base//button" .
            "[contains(text(),{$literal}) " .
            "or contains(@id,{$literal}) " .
            "or contains(@class, {$literal}) " .
            "or contains(@name,{$literal})]"
        );

        Assertion::assertNotNull( $el, "Couldn't find '$button' button" );

        $el->click();
    }

    /**
     * @Then /^on "([^"]*)" I see (\d+) links$/
     */
    public function onSomePlaceISeeNumberOfLinks( $somePlace, $totalLinks )
    {
        $base = $this->makeXpathForBlock( $somePlace );

        $allLinks = $this->getSession()->getPage()->findAll( "xpath", "{$base}//a[@href]" );

        Assertion::assertEquals( count( $allLinks ), $totalLinks );
    }

    /**
     * @Then /^I (?:don\'t|do not) see (?:a |)"([^"]*)" link$/
     */
    public function iDontSeeALink( $link )
    {
        $this->onSomePlaceIDonTSeeALink( 'main', $link );
    }

    /**
     * @Then /^on "([A-Za-z\s]*)" I (?:don\'t|do not) see a "([^"]*)" link$/
     */
    public function onSomePlaceIDonTSeeALink( $somePlace, $link )
    {
        $xpath = $this->makeXpathForBlock( $somePlace );

        $literal = $this->literal( $link );

        $el = $this->getSession()->getPage()->find(
            "xpath",
            "//a[contains( text(), $literal ) or contains( @href, $literal )][@href]"
        );

        Assertion::assertNotNull( $el, "Link '$link' not found" );
    }

    /**
     * @Then /^I (?:don\'t|do not) see links(?:|\:)$/
     */
    public function iDonTSeeLinks( TableNode $table )
    {
        $this->onSomePlaceIDonTSeeTheLinks( 'main', $table );
    }

    /**
     * @Then /^on "([A-Za-z\s]*)" I (?:don\'t|do not) see the links:$/
     */
    public function onSomePlaceIDonTSeeTheLinks( $somePlace, TableNode $table )
    {
        $rows = $table->getRows();
        array_shift( $rows );   // this is needed to take the first row ( readability only )

        $base = $this->makeXpathForBlock( $somePlace );
        foreach ( $rows as $row )
        {
            $link = $row[0];
            $literal = $this->literal( $link );
            $el = $this->getSession()->getPage()->find( "xpath", "$base//a[text() = $literal][@href]" );

            Assertion::assertNull( $el, "Unexpected link found" );
        }
    }

    /**
     * @Then /^I (?:don\'t|do not) see(?: the|) "([A-Za-z\s]*)" menu$/
     */
    public function iDonTSeeSomeMenu( $menu )
    {
        $this->iDonTSeeSomeElement( "$menu menu" );
    }

    /**
     * @Then /^I (?:don\'t|do not) see(?: the|) "([A-Za-z\s]*)" element$/
     */
    public function iDonTSeeSomeElement( $element )
    {
        $xpath = $this->makeXpathForBlock( $element );
        if ( empty( $xpath ) )
        {
            throw new PendingException( "Element '$element' not defined" );
        }

        $el = $this->getSession()->getPage()->find( "xpath", $xpath );

        Assertion::assertNull( $el, "Element '$element' was unexpectly found" );
    }

    /**
     * @Then /^on "([^"]*)" I see "([^"]*)" video$/
     */
    public function onSomePlaceISeeVideo( $somePlace, $video )
    {
        //TODO: Check Selenium behaviour
        $base = $this->makeXpathForBlock( $somePlace );

        $videoSource = $this->getPathByFileSource( $video );
        $el = $this->getSession()->getPage()->find( "xpath", "{$base}//video//source" );

        Assertion::assertNotNull( $el, "Video object {$video} not found" );
        Assertion::assertEquals( $el->getAttribute( 'src' ), $videoSource );
    }

    /**
     * @Given /^(?:|I )am (?:at|on) (?:|the) "([^"]*)" page$/
     * @When  /^(?:|I )go to (?:|the) "([^"]*)"(?:| page)$/
     */
    public function iGoToThe( $pageIdentifier )
    {
        return array(
            new Step\When( 'I am on "' . $this->getPathByPageIdentifier( $pageIdentifier ) . '"' ),
        );
    }

    /**
     * @When /^(?:|I )search for "([^"]*)"$/
     */
    public function iSearchFor( $searchPhrase )
    {
        $session = $this->getSession();
        $searchField = $session->getPage()->findById( 'site-wide-search-field' );

        Assertion::assertNotNull( $searchField, 'Search field not found.' );

        $searchField->setValue( $searchPhrase );

        // Ideally, using keyPress(), but doesn't work since no keypress handler exists
        // http://sahi.co.in/forums/discussion/2717/keypress-in-java/p1
        //     $searchField->keyPress( 13 );
        //
        // Using JS instead:
        // Note:
        //     $session->executeScript( "$('#site-wide-search').submit();" );
        // Gives:
        //     error:_call($('#site-wide-search').submit();)
        //     SyntaxError: missing ) after argument list
        //     Sahi.ex@http://<hostname>/_s_/spr/concat.js:3480
        //     @http://<hostname>/_s_/spr/concat.js:3267
        // Solution: Encapsulating code in a closure.
        // @todo submit support where recently added to MinkCoreDriver, should us it when the drivers we use support it
        try
        {
            $session->executeScript( "(function(){ $('#site-wide-search').submit(); })()" );
        }
        catch ( MinkUnsupportedDriverActionException $e )
        {
            // For drivers not able to do javascript we assume we can click the hidden button
            $searchField->getParent()->findButton( 'SearchButton' )->click();
        }

        // Store for reuse in result page
        $this->priorSearchPhrase = $searchPhrase;
    }

    /**
     * @Then /^(?:|I )see search (\d+) result$/
     */
    public function iSeeSearchResults( $arg1 )
    {
        $resultCountElement = $this->getSession()->getPage()->find( 'css', 'div.feedback' );

        Assertion::assertNotNull(
            $resultCountElement,
            'Could not find result count text element.'
        );

        Assertion::assertEquals(
            "Search for \"{$this->priorSearchPhrase}\" returned {$arg1} matches",
            $resultCountElement->getText()
        );
    }

    /**
     * @Then /^I see "([^"]*)" button$/
     */
    public function iSeeButton( $button )
    {
        Assertion::assertNotNull(
            $this->getSession()->getPage()->findButton( $button ),
            "Could not find '$button' button."
        );
    }

    /**
     * @Then /^I see (?:a |)checkbox field with "([^"]*)" label$/
     */
    public function iSeeCheckboxFieldWithLabel( $label )
    {
        $elements = $this->getSession()->getPage()->findAll(
            "xpath",
            "//input[@type = 'checkbox']/.."
        );

        Assertion::assertNotEquals( count( $elements ), 0, "Coudn't find any checkbox" );

        $found = false;
        for ( $i = 0; $i < count( $elements ) && !$found; $i++ )
        {
            if ( strpos( $elements[$i]->getText(), $label ) !== false )
            {
                $found = true;
            }
        }

        // assert that it was found
        Assertion::assertEquals(
            true,
            $found,
            "Couldn't find a checkbox with label '$label'"
        );
    }

    /**
     * @Then /^I see (?:the|a|an) "([^"]*)" element$/
     */
    public function iSeeAnElement( $element )
    {
        $this->onSomePlaceISeeAnElement( 'main', $element );
    }

    /**
     * @Then /^on "([A-Za-z\s]*)" I see (?:the|a|an) "([^"]*)" element$/
     *
     * Element represents divs special chars, everything on browser that can't be
     * found through simple text content
     */
    public function onSomePlaceISeeAnElement( $somePlace, $element )
    {
        $base = $this->makeXpathForBlock( $somePlace );

        // an element can't be search through content, so lets find through
        // id, class, name, src or href
        $literal = $this->literal( $element );
        $el = $this->getSession()->getPage()->find(
            "xpath",
            "$base//*["
            . "contains( @id, $literal )"
            . "or contains( @class, $literal )"
            . "or contains( @name, $literal )"
            . "or contains( @src, $literal )"
            . "or contains( @href, $literal )"
            . "]"
        );

        Assertion::assertNotNull( $el, "Element '$element' not found" );
    }

    /**
     * @Then /^I see "([^"]*)" error$/
     */
    public function iSeeError( $error )
    {
        $el = $this->getSession()->getPage()->find(
            "xpath",
            "//*[contains( @class, 'warning' ) or contains( @class, 'error' )]"
            . "//*[contains( text(), " . $this->literal( $error ) . " )]"
        );

        Assertion::assertNotNull( $el, "Couldn't find error message '$error'" );
        Assertion::assertContains( $error, $el->getText(), "Couldn't find error message '$error'" );
    }

    /**
     * @Then /^I see key "([^"]*)" with (?:value |)"([^"]*)"$/
     */
    public function iSeeKeyWithValue( $key, $value )
    {
        $el = $this->getSession()->getPage()->findAll(
            "xpath",
            "//*[contains( text(), " . $this->literal( $key ) . " )]"
        );

        Assertion::assertNotNull( $el, "Couldn't find tag with '$key' text" );

        $found = false;
        for ( $i = 0; $i < count( $el ) && !$found; $i++ )
        {
            $found = strpos( $el[$i]->getParent()->getText(), $value );
        }

        Assertion::assertNotEquals( false, $found, "Couldn't find a key '$key' with value '$value'" );
    }

    /**
     * @Then /^I see "([^"]*)" link$/
     */
    public function iSeeLink( $link )
    {
        $this->onSomePlaceISeeLink( 'main', $link );
    }

    /**
     * @Then /^on "([A-Za-z\s]*)" I see (?:the|an|a) "([^"]*)" link$/
     */
    public function onSomePlaceISeeLink( $somePlace, $link )
    {
        $base = $this->makeXpathForBlock( $somePlace );
        Assertion::assertNotNull( $link, "Missing link for searching on table" );

        $literal = $this->literal( $link );
        $el = $this->getSession()->getPage()->find(
            "xpath",
            "$base//a[contains( text(), $literal )][@href]"
        );
        Assertion::assertNotNull( $el, "Couldn't find a link for object '$link'" );
    }

    /**
     * @Then /^(?:|I )see links for Content objects(?:|\:)$/
     */
    public function iSeeLinksForContentObjects( TableNode $table )
    {
        $this->onSomePlaceISeeTheLinksForContentObjects( 'main', $table );
    }

    /**
     * @Given /^on "([A-Za-z\s]*)" I see the links for Content objects(?:|\:)$/
     *
     * @todo check the parents (if defined)
     */
    public function onSomePlaceISeeTheLinksForContentObjects( $somePlace, TableNode $table )
    {
        $rows = $table->getRows();
        array_shift( $rows );   // this is needed to take the first row ( readability only )

        $links = $parents = array();
        foreach ( $rows as $row )
        {
            if ( count( $row ) >= 2 )
            {
                list( $links[], $parents[] ) = $row;
            }
            else
            {
                $links[] = $row[0];
            }
        }

        // check links
        $this->checkLinksForContentObjects( $links, $somePlace );

        // to end the assertion, we need to verify parents (if specified)
//        if ( !empty( $parents ) )
//            $this->checkLinkParents( $links, $parents );
    }

    /**
     * Find the links passed, assert they exist in the specified place
     *
     * @param array  $links The links to be asserted
     * @param string $where The place where should search for the links
     *
     * @todo verify if the links are for objects
     * @todo check if it has a different url alias
     */
    protected function checkLinksForContentObjects( array $links, $where )
    {
        $base = $this->makeXpathForBlock( $where );
        foreach ( $links as $link )
        {
            Assertion::assertNotNull( $link, "Missing link for searching on table" );

            $literal = $this->literal( $link );
            $el = $this->getSession()->getPage()->find(
                "xpath",
                "$base//a[contains( text(),$literal )][@href]"
            );

            Assertion::assertNotNull( $el, "Couldn't find a link for object '$link'" );
        }
    }

    /**
     * @Then /^I see links:$/
     */
    public function iSeeLinks( TableNode $table )
    {
        $this->onSomeSeeIPlaceLinks( 'main', $table );
    }

    /**
     * @Then /^on "([A-Za-z\s]*)" I see links:$/
     */
    public function onSomePlaceISeeLinks( $somePlace, TableNode $table )
    {
        $base = $this->makeXpathForBlock( $somePlace );
        // get all links
        $available = $this->getSession()->getPage()->findAll( "xpath", "$base//a[@href]" );

        $rows = $table->getRows();
        array_shift( $rows );   // this is needed to take the first row ( readability only )
        // remove links from embeded arrays
        $links = array();
        foreach ( $rows as $row )
            $links[] = $row[0];

        // and finaly verify their existence
        $this->checkLinksExistence( $links, $available );
    }

    /**
     * Check existence of links
     *
     * @param array         $links
     * @param NodeElement[] $available
     */
    protected function checkLinksExistence( array $links, array $available )
    {
        // verify if every required link is in available
        foreach ( $links as $link )
        {
            $name = $link;
            $url = str_replace( ' ', '-', $name );

            $i = 0;
            while (
                !empty( $available[$i] )
                && strpos( $available[$i]->getattribute( "href" ), $url ) === false
                && strpos( $available[$i]->getText(), $name ) === false
            )
                $i++;

            $test = !null;
            if ( empty( $available[$i] ) )
            {
                $test = null;
            }

            // check if the link was found or the $i >= $count
            Assertion::assertNotNull( $test, "Couldn't find '$name' link" );
        }
    }

    /**
     * @Then /^(?:|I )see links for Content objects in following order(?:|\:)$/
     */
    public function iSeeLinksForContentObjectsInFollowingOrder( TableNode $table )
    {
        $this->onSomePlaceISeeLinksInFollowingOrder( 'main', $table );
    }

    /**
     * @Then /^on "([A-Za-z\s]*)" I see links in following order:$/
     *
     *  @todo check "parent" node
     */
    public function onSomePlaceISeeLinksInFollowingOrder( $somePlace, TableNode $table )
    {
        $base = $this->makeXpathForBlock( $somePlace );
        // get all links
        $available = $this->getSession()->getPage()->findAll( "xpath", "$base//a[@href]" );

        $rows = $table->getRows();
        array_shift( $rows );   // this is needed to take the first row ( readability only )
        // make link and parent arrays:
        $links = $parents = array();
        foreach ( $rows as $row )
        {
            if ( count( $row ) >= 2 )
            {
                list( $links[], $parents[] ) = $row;
            }
            else
            {
                $links[] = $row[0];
            }
        }

        // now verify the link order
        $this->checkLinkOrder( $links, $available );

        // to end the assertion, we need to verify parents (if specified)
//        if ( !empty( $parents ) )
//            $this->checkLinkParents( $links, $parents );
    }

    /**
     * Checks if links show up in the following order
     * Notice: if there are 3 links and we omit the middle link it will also be
     *  correct. It only checks order, not if there should be anything in
     *  between them
     *
     * @param array         $links
     * @param NodeElement[] $available
     */
    protected function checkLinkOrder( array $links, array $available )
    {
        $i = $passed = 0;
        $last = '';
        foreach ( $links as $link )
        {
            $name = $link;
            $url = str_replace( ' ', '-', $name );

            // find the object
            while (
                !empty( $available[$i] )
                && strpos( $available[$i]->getAttribute( "href" ), $url ) === false
                && strpos( $available[$i]->getText(), $name ) === false
            )
                $i++;

            $test = !null;
            if ( empty( $available[$i] ) )
            {
                $test = null;
            }

            // check if the link was found or the $i >= $count
            Assertion::assertNotNull( $test, "Couldn't find '$name' after '$last'" );

            $passed++;
            $last = $name;
        }

        Assertion::assertEquals(
            count( $links ),
            $passed,
            "Expected to evaluate '{count( $links )}' links evaluated '{$passed}'"
        );
    }

    /**
     * @Then /^(?:|I )see links in(?:|\:)$/
     */
    public function iSeeLinksIn( TableNode $table )
    {
        $session = $this->getSession();
        $rows = $table->getRows();
        array_shift( $rows );   // this is needed to take the first row ( readability only )
        foreach ( $rows as $row )
        {
            // prepare data
            Assertion::assertEquals(
                count( $row ), 2,
                "The table should be have array with link and tag"
            );
            list( $link, $type ) = $row;

            // make xpath
            $literal = $this->literal( $link );
            $xpath = $this->concatTagsWithXpath(
                $this->getTagsFor( $type ),
                "//a[@href and text() = $literal]"
            );

            $el = $session->getPage()->find( "xpath", $xpath );

            Assertion::assertNotNull( $el, "Couldn't find a link with '$link' text" );
        }
    }

    /**
     * @Then /^(?:|I )see (\d+) "([^"]*)" elements listed$/
     */
    public function iSeeListedElements( $count, $objectType )
    {
        $objectListTable = $this->getSession()->getPage()->find(
            'xpath',
            '//table[../h1 = "' . $objectType . ' list"]'
        );

        Assertion::assertNotNull(
            $objectListTable,
            'Could not find listing table for ' . $objectType
        );

        Assertion::assertCount(
            $count + 1,
            $objectListTable->findAll( 'css', 'tr' ),
            'Found incorrect number of table rows.'
        );
    }

    /**
     * @Then /^I see (?:a |)"([^"]*)" key$/
     */
    public function iSeeKey( $key )
    {
        $this->onSomePlaceISeeAKey( 'main', $key );
    }

    /**
     * @Then /^on "([A-Za-z\s]*)" I see a "([^"]*)" key$/
     */
    public function onSomePlaceISeeAKey( $somePlace, $key )
    {
        $base = $this->makeXpathForBlock( $somePlace );

        $literal = $this->literal( $key );

        // get the key
        // using 'contains' will help for the cases where there are spaces of \n on
        // the tag, and if used '=' it wouldn't be found
        $el = $this->getSession()->getPage()->find( "xpath", "$base//*[contains( text(), $literal )]" );

        Assertion::assertNotNull( $el, "Couldn't find '$key' key" );
        Assertion::assertEquals( trim( $el->getText() ), $key, "Couldn't find '$key' key" );
    }

    /**
     * @Then /^I see "([^"]*)" message$/
     */
    public function iSeeMessage( $text )
    {
        return array( new Step\Then( "I should see \"$text\"" ) );
    }

    /**
     * @Then /^I see (?:the |)"([A-Za-z\s]*)" menu$/
     */
    public function iSeeSomeMenu( $menu )
    {
        $this->iSeeSomeElement( "$menu menu" );
    }

    /**
     * @Then /^I see (?:the |)"([A-Za-z\s]*)" element$/
     */
    public function iSeeSomeElement( $element )
    {
        $xpath = $this->makeXpathForBlock( $element );
        if ( empty( $xpath ) )
        {
            throw new PendingException( "Element '$element' is not defined" );
        }

        $el = $this->getSession()->getPage()->find( "xpath", $xpath );

        Assertion::assertNotNull( $el, "Element '$element' not found" );
    }

    /**
     * @Then /^I see table with:$/
     */
    public function iSeeTableWith( TableNode $table )
    {
        $rows = $table->getRows();
        $headers = array_shift( $rows );

        $max = count( $headers );
        $mainHeader = array_shift( $headers );
        foreach ( $rows as $row )
        {
            $mainColumn = array_shift( $row );
            $foundRows = $this->getTableRow( $mainColumn, $mainHeader );

            $found = false;
            $maxFound = count( $foundRows );
            for ( $i = 0; $i < $maxFound && !$found; $i++ )
            {
                if ( $this->assertTableRow( $foundRows[$i], $row, $headers ) )
                {
                    $found = true;
                }
            }

            $message = "Couldn't find row with elements: '" . implode( ",", array_merge( array( $mainColumn ), $row ) ) . "'";
            Assertion::assertTrue( $found, $message );
        }
    }

    /**
     * Verifies if a row as the expected columns, position of columns can be added
     * for a more accurated assertion
     *
     * @param \Behat\Mink\Element\NodeElement  $row              Table row node element
     * @param string[]                         $columns          Column text to assert
     * @param string[]|int[]                   $columnsPositions Columns positions in int or string (number must be in string)
     *
     * @return boolean
     */
    protected function assertTableRow( NodeElement $row, array $columns, array $columnsPositions = null )
    {
        // find which kind of column is in this row
        $elType = $row->find( 'xpath', "/th" );
        $type = ( empty( $elType ) ) ? '/td' : '/th';

        $max = count( $columns );
        for ( $i = 0; $i < $max; $i++ )
        {
            $position = "";
            if ( !empty( $columnsPositions[$i] ) )
            {
                $position = "[{$this->getNumberFromString( $columnsPositions[$i] )}]";
            }

            $el = $row->find( "xpath", "$type$position" );

            // check if match with expected if not return false
            if ( $el === null || $columns[$i] !== $el->getText() )
            {
                return false;
            }
        }

        // if we're here then it means all have ran as expected
        return true;
    }

    /**
     * Find a(all) table row(s) that match the column text
     *
     * @param string        $text       Text to be found
     * @param string|int    $column     In which column the text should be found
     * @param string        $tableXpath If there is a specific table
     *
     * @return Behat\Mink\Element\NodeElement[]
     */
    protected function getTableRow( $text, $column = null, $tableXpath = null )
    {
        // check column
        if ( !empty( $column ) )
        {
            if ( is_integer( $column ) )
            {
                $columnNumber = "[$column]";
            }
            else
            {
                $columnNumber = "[{$this->getNumberFromString( $column )}]";
            }
        }
        else
        {
            $columnNumber = "";
        }

        // get all possible elements
        $elements = array_merge(
            $this->getSession()->getPage()->findAll( "xpath", "$tableXpath//tr/th" ),
            $this->getSession()->getPage()->findAll( "xpath", "$tableXpath//tr/td" )
        );

        $foundXpath = array();
        $total = count( $elements );
        $i = 0;
        while ( $i < $total )
        {
            if ( strpos( $elements[$i]->getText(), $text ) !== false )
            {
                $foundXpath[] = $elements[$i]->getParent();
            }

            $i++;
        }

        return $foundXpath;
    }

    /**
     * @Then /^I see "([^"]*)" text emphasized$/
     */
    public function iSeeTextEmphasized( $text )
    {
        $this->onSomePlaceISeeTextEmphasized( 'main', $text );
    }

    /**
     * @Then /^on "([A-Za-z\s]*)" I see (?:the |)"([^"]*)" text emphasized$/
     */
    public function onSomePlaceISeeTextEmphasized( $somePlace, $text )
    {
        // first find the text
        $base = $this->makeXpathForBlock( $somePlace );
        $el = $this->getSession()->getPage()->findAll( "xpath", "$base//*[contains( text(), {$this->literal( $text )} )]" );
        Assertion::assertNotNull( $el, "Coudn't find text '$text' at '$somePlace' content" );

        // verify only one was found
        Assertion::assertEquals( count( $el ), 1, "Expecting to find '1' found '" . count( $el ) . "'" );

        // finaly verify if it has custom charecteristics
        Assertion::assertTrue(
            $this->assertElementEmphasized( $el[0] ),
            "The text '$text' isn't emphasized"
        );
    }

    /**
     * Verifies if the element has 'special' configuration on a attribute (default -> style)
     *
     * @param \Behat\Mink\Element\NodeElement  $el              The element that we want to test
     * @param string                           $characteristic  Verify a specific characteristic from attribute
     * @param string                           $attribute       Verify a specific attribute
     *
     * @return boolean
     */
    protected function assertElementEmphasized( NodeElement $el, $characteristic = null, $attribute = "style" )
    {
        // verify it has the attribute we're looking for
        if ( !$el->hasAttribute( $attribute ) )
        {
            return false;
        }

        // get the attribute
        $attr = $el->getAttribute( $attribute );

        // check if want to test specific characteristic and if it is present
        if ( !empty( $characteristic ) && strpos( $attr, $characteristic ) === false )
        {
            return false;
        }

        // if we're here it is emphasized
        return true;
    }

    /**
     * @Then /^(?:|I )see "(.+)" (?:title|topic)$/
     */
    public function iSeeTitle( $title )
    {
        $literal = $this->literal( $title );
        $xpath = $this->concatTagsWithXpath(
            $this->getTagsFor( "title" ),
            "[text() = $literal]"
        );

        $el = $this->getSession()->getPage()->find( "xpath", $xpath );

        // assert that message was found
        Assertion::assertNotNull( $el, "Could not find '$title' title." );
        Assertion::assertContains(
            $title,
            $el->getText(),
            "Couldn't find '$title' title in '{$el->getText()}'"
        );
    }

    /**
     * @Then /^(?:|I )should be redirected to "([^"]*)"$/
     */
    public function iShouldBeRedirectedTo( $redirectTarget )
    {
        $redirectForm = $this->getSession()->getPage()->find( 'css', 'form[name="Redirect"]' );

        Assertion::assertNotNull( $redirectForm, 'Missing redirect form.' );
        Assertion::assertEquals(
            $redirectTarget,
            $redirectForm->getAttribute( 'action' )
        );
    }

    /**
     * @Then /^(?:|I )want dump of (?:|the) page$/
     */
    public function iWantDumpOfThePage()
    {
        echo $this->getSession()->getPage()->getContent();
    }
}
