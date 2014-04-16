<?php
/**
 * File containing the BrowserInternalSentences class.
 *
 * This interface contains the browser internal sentences that will match some
 * action or assertion for browser testing
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Features\Context\Browser;

use Behat\Gherkin\Node\TableNode;

/**
 * BrowserInternalSentences
 *
 * @todo Add examples and/or explanations for not so easy to understand sentences
 * @todo Add example tables to the sentences that use TableNode
 */
interface BrowserInternalSentences
{
    /**
     * @Given /^I checked "(?P<label>[^"]*)" checkbox$/
     * @When /^I check "(?P<label>[^"]*)" checkbox$/
     */
    public function checkOption( $option );

    /**
     * @Given /^I clicked (?:on|at) (?:the |)"(?P<button>[^"]*)" button$/
     * @When /^I click (?:on|at) (?:the |)"(?P<button>[^"]*)" button$/
     */
    public function iClickAtButton( $button );

    /**
     * @Given /^on "(?P<pagePart>[A-Za-z0-9\s-_]*)" I clicked (?:at |on |)"(?P<button>[^"]*)" button$/
     * @When /^on "(?P<pagePart>[A-Za-z0-9\s-_]*)" I click (?:at |on |)"(?P<button>[^"]*)" button$/
     */
    public function onPageSectionIClickAtButton( $pageSection, $button );

    /**
     * @Given /^I clicked (?:on|at) (?:the |)"(?P<link>[^"]*)" link$/
     * @When /^I click (?:on|at) (?:the |)"(?P<link>[^"]*)" link$/
     */
    public function iClickAtLink( $link );

    /**
     * @Given /^on "(?P<pagePart>[A-Za-z0-9\s-_]*)" I clicked (?:on|at) "(?P<link>[^"]*)" link$/
     * @When /^on "(?P<pagePart>[A-Za-z0-9\s-_]*)" I click (?:on|at) "(?P<link>[^"]*)" link$/
     */
    public function onPageSectionIClickAtLink( $pageSection, $link );

    /**
     * @Given /^I filled form with(?:|\:)$/
     * @When /^I fill form with(?:|\:)$/
     */
    public function iFillFormWith( TableNode $table );

    /**
     * @Given /^I am (?:at|on) (?:|the) "(?P<page>[^"]*)" page$/
     * @When  /^I go to (?:|the) "(?P<page>[^"]*)"(?:| page)$/
     */
    public function iGoToThe( $pageIdentifier );

    /**
     * @When /^I search for "(?P<searchPhrase>[^"]*)"$/
     */
    public function iSearchFor( $searchPhrase );

    /**
     * @When /^I select "(?P<option>[^"]*)"$/
     *
     * IMPORTANT:
     *  This will thrown an error if it find's more than 1 select/dropdown on page
     */
    public function iSelect( $option );

    /**
     * @Given /^I selected "(?P<label>[^"]*)" radio button$/
     * @When /^I select "(?P<abel>[^"]*)" radio button$/
     */
    public function iSelectRadioButon( $label );

    /**
     * @When /^I follow the redirection$/
     * @Then /^I should be redirected$/
     */
    public function iFollowTheRedirection();

    /**
     * @Then /^I see "(?P<button>[^"]*)" button$/
     */
    public function iSeeButton( $button );

    /**
     * @Then /^I see (?:a |)checkbox field with "(?P<label>[^"]*)" label$/
     */
    public function iSeeCheckboxFieldWithLabel( $label );

    /**
     * @Then /^I see (?:the|a|an) "(?P<element>[^"]*)" element$/
     *
     * @param string $element This should be an id|class|name|src|href since it will attempt to find html that might not have any text
     */
    public function iSeeElement( $element );

    /**
     * @Then /^on "(?P<pagePart>[A-Za-z0-9\s-_]*)" I see (?:the|a|an) "(?P<element>[^"]*)" element$/
     */
    public function onPageSectionISeeElement( $pageSection, $element );

    /**
     * @Then /^I see (?P<total>\d+) "(?P<objectType>[^"]*)" elements listed$/
     *
     * This is used to count rows for an object type
     *
     * @todo make an explicit example
     */
    public function iSeeTotalElements( $total, $objectType );

    /**
     * @Then /^I (?:don\'t|do not) see(?: the|) "(?P<element>[A-Za-z\s]*)" element$/
     */
    public function iDonTSeeElement( $element );

    /**
     * @Then /^on "(?P<pagePart>[A-Za-z0-9\s-_]*)" I (?:don\'t|do not) see (?:the|a|an) "(?P<element>[^"]*)" element$/
     */
    public function onPageSectionIDontSeeElement( $pageSection, $element );

    /**
     * @Then /^I see "(?P<warning>[^"]*)" (?:warning|error)$/
     */
    public function iSeeWarning( $warning );

    /**
     * @Then /^I see (?:the |)exact "(?P<text>[^"]*)" (?:message|text|key)$/
     */
    public function iSeeText( $text );

    /**
     * @Then /^on "(?P<pagePart>[A-Za-z0-9\s-_]*)" I see (?:the |)exact "(?P<text>[^"]*)" (?:message|text|key)$/
     */
    public function onPageSectionISeeText( $pageSection, $text );

    /**
     * @Then /^I see "(?P<key>[^"]*)" key with "(?P<value>[^"]*)"(?: value|)$/
     *
     * Examples:
     *  - Then I see "Username" key with value "myusername"
     */
    public function iSeeKeyWithValue( $key, $value );

    /**
     * @Then /^I see "(?P<link>[^"]*)" link$/
     */
    public function iSeeLink( $link );

    /**
     * @Then /^on "(?P<pagePart>[A-Za-z0-9\s-_]*)" I see (?:the|an|a) "(?P<link>[^"]*)" link$/
     */
    public function onPageSectionISeeLink( $pageSection, $link );

    /**
     * @Then /^I see links(?:|\:)$/
     *      | link          |
     *      | some link     |
     *      | another link  |
     *      ...
     *      | the link      |
     */
    public function iSeeLinks( TableNode $table );

    /**
     * @Then /^on "(?P<pagePart>[A-Za-z0-9\s-_]*)" I see links(?:|\:)$/
     *
     */
    public function onPageSectionISeeLinks( $pageSection, TableNode $table );

    /**
     * @Then /^I see links for Content objects(?:|\:)$/
     */
    public function iSeeLinksForContentObjects( TableNode $table );

    /**
     * @Then /^on "(?P<pagePart>[A-Za-z0-9\s-_]*)" I see the links for Content objects(?:|\:)$/
     *      | link    | content    |
     *      | link A  | content A  |
     *      | BLink   | BContent   |
     *      ...
     *      | another | Some other |
     */
    public function onPageSectionISeeTheLinksForContentObjects( $pageSection, TableNode $table );

    /**
     * @Then /^I see links in following order(?:|\:)$/
     *      | ordered links |
     *      | link 1        |
     *      | link 2        |
     *      ...
     *      | link N        |
     */
    public function iSeeLinksInFollowingOrder( TableNode $table );

    /**
     * @Then /^on "(?P<pagePart>[A-Za-z0-9\s-_]*)" I see links in following order(?:|\:)$/
     */
    public function onPageSectionISeeLinksInFollowingOrder( $pageSection, TableNode $table );

    /**
     * @Then /^on "(?P<pagePart>[A-Za-z0-9\s-_]*)" I see (?P<total>\d+) links$/
     *
     * Examples:
     *  - Then on "breadcrumb" I see 7 links
     */
    public function onPageSectionISeeTotalLinks( $pageSection, $totalLinks );

    /**
     * @Then /^(?:|I )see links in(?:|\:)$/
     *      | link  | tag   |
     *      | link1 | title |
     *      | link2 |       |
     *      | link3 | text  |
     *
     * Example: this is used to see in tag cloud which tags have more results and
     *      which have less
     */
    public function iSeeLinksInTag( TableNode $table );

    /**
     * @Then /^I (?:don\'t|do not) see "(?P<link>[^"]*)" link$/
     */
    public function iDontSeeLink( $link );

    /**
     * @Then /^on "(?P<pagePart>[A-Za-z0-9\s-_]*)" I (?:don\'t|do not) see "(?P<link>[^"]*)" link$/
     */
    public function onPageSectionIDonTSeeLink( $pageSection, $link );

    /**
     * @Then /^I (?:don\'t|do not) see links(?:|\:)$/
     */
    public function iDonTSeeLinks( TableNode $table );

    /**
     * @Then /^on "(?P<pagePart>[A-Za-z0-9\s-_]*)" I (?:don\'t|do not) see the links(?:|\:)$/
     */
    public function onPageSectionIDonTSeeLinks( $pageSection, TableNode $table );

    /**
     * @Then /^I see "(?P<message>[^"]*)" (?:message|text)$/
     */
    public function iSeeMessage( $text );

    /**
     * @Given /^I (?:don\'t|do not) see "(?P<text>[^"]*)" message$/
     */
    public function iDonTSeeMessage( $message );

    /**
     * @Then /^I see "(?P<page>[^"]*)" page$/
     */
    public function iSeePage( $pageIdentifier );

    /**
     * @Then /^I see search (?P<total>\d+) result$/
     */
    public function iSeeSearchResults( $total );

    /**
     * @Then /^I see table with(?:|\:)$/
     *      | Column 1 | Column 2 | Column 4 |
     *      | Value A  | Value B  | Value D  |
     *      ...
     *      | Value I  | Value J  | Value L  |
     *
     * The table header needs to have the number of the column which column
     * values belong, all the other text is optional, normaly using 'Column' for
     * easier understanding
     */
    public function iSeeTableWith( TableNode $table );

    /**
     * @Then /^I see "(?P<text>[^"]*)" text emphasized$/
     */
    public function iSeeTextEmphasized( $text );

    /**
     * @Then /^on "(?P<pagePart>[A-Za-z0-9\s-_]*)" I see (?:the |)"(?P<text>[^"]*)" text emphasized$/
     */
    public function onPageSectionISeeTextEmphasized( $pageSection, $text );

    /**
     * @Then /^I see "(?P<title>[^"]*)" (?:title|topic)$/
     */
    public function iSeeTitle( $title );

    /**
     * @Then /^on "(?P<pagePart>[A-Za-z0-9\s-_]*)" I see "(?P<video>[^"]*)" video$/
     */
    public function onPageSectionISeeVideo( $pageSection, $video );

    /**
     * @Then /^I should be redirected to "(?P<target>[^"]*)"$/
     */
    public function iShouldBeRedirectedTo( $redirectTarget );
}
