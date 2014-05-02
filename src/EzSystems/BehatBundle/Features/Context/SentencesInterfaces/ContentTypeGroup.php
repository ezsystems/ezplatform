<?php
/**
 * File containing the ContentTypeGroup interface.
 * 
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Features\Context\SentencesInterfaces;

use Behat\Gherkin\Node\TableNode;

/**
 * ContentTypeGroup Sentences Interface
 *
 * This interface has the sentences definitions for the ContentTypeGroup steps
 */
interface ContentTypeGroup
{
    /**
     * @When /^I create a Content Type Group with identifier "(?P<identifier>[^"]*)"$/
     */
    public function iCreateContentTypeGroup( $identifier );

    /**
     * @When /^I read ContentTypeGroups list$/
     */
    public function iReadContentTypeGroupsList();

    /**
     * @Then /^I see a Content Type Group with identifier "(?P<identifier>[^"]*)"$/
     */
    public function iSeeContentTypeGroup( $identifier );

    /**
     * @Then /^I see (?P<total>\d+) Content Type Group(?:s|) with identifier "(?P<identifier>[^"]*)"$/
     */
    public function iSeeTotalContentTypeGroup( $total, $identifier );

    /**
     * @Then /^I see the following Content Type Groups:$/
     */
    public function iSeeTheFollowingContentTypeGroups( TableNode $groups );
}
