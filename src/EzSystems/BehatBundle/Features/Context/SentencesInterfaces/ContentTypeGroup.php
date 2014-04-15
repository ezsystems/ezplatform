<?php
/**
* File containing the ContentTypeGroup class.
*
* This interface has the sentences definitions for the ContentTypeGroup steps
*
* @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
* @version //autogentag//
*/

namespace EzSystems\BehatBundle\Features\Context\SentencesInterfaces;

use Behat\Gherkin\Node\TableNode;

/**
 * ContentTypeGroup Sentences Interface
 */
interface ContentTypeGroup
{
    /**
     * @Given /^I have (?:a |)Content Type Group (?P<identifier>[A-Z])$/
     * @Given /^I have (?:a |)Content Type Group with "(?P<identifier>[^"])" identifier$/
     */
    public function iHaveContentTypeGroup( $identifier );

    /**
     * @Given /^I (?:do not|don\'t) have a Content Type Group (?P<identifier>[A-Z])$/
     * @Given /^I (?:do not|don\'t) have a Content Type Group with "(?P<identifier>[^"])" identifier$/
     */
    public function iDonTHaveContentTypeGroup( $identifier );

    /**
     * @When /^I create a Content Type Group (?P<identifier>[A-Z])$/
     * @When /^I create a Content Type Group with "(?P<identifier>[^"])" identifier$/
     */
    public function iCreateContentTypeGroup( $identifier );

    /**
     * @Then /^I see a Content Type Group (?P<identifier>[A-Z])$/
     * @Then /^I see a Content Type Group with "(?P<identifier>[^"])" identifier$/
     */
    public function iSeeContentTypeGroup( $identifier );

    /**
     * @Then /^I see (?P<total>\d+) Content Type Group(?:s|) (?P<identifier>[A-Z])$/
     * @Then /^I see (?P<total>\d+) Content Type Group(?:s|) with "(?P<identifier>[^"])" identifier$/
     */
    public function iSeeTotalContentTypeGroup( $total, $identifier );
}
