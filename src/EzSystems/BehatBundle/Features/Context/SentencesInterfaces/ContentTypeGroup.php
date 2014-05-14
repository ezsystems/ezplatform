<?php
/**
 * File containing the ContentTypeGroup interface.
 * 
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
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
     * @When /^I read Content Type Group with identifier "(?P<identifier>[^"]*)"$/
     */
    public function iReadContentTypeGroup( $identifier );

    /**
     * @When /^I read Content Type Groups list$/
     */
    public function iReadContentTypeGroupsList();

    /**
     * @When /^I update Content Type Group with identifier "(?P<actualIdentifier>[^"]*)" to "(?P<newIdentifier>[^"]*)"$/
     */
    public function iUpdateContentTypeGroupIdentifier( $actualIdentifier, $newIdentifier );

    /**
     * @When /^I delete Content Type Group with identifier "(?P<identifier>[^"]*)"$/
     */
    public function iDeleteContentTypeGroup( $identifier );

    /**
     * @Then /^I see a Content Type Group with identifier "(?P<identifier>[^"]*)"$/
     */
    public function iSeeContentTypeGroup( $identifier );

    /**
     * @Then /^I don\'t see a Content Type Group with identifier "(?P<identifier>[^"]*)"$/
     */
    public function iDonTSeeAContentTypeGroup( $identifier );

    /**
     * @Then /^I see (?P<total>\d+) Content Type Group(?:s|) with identifier "(?P<identifier>[^"]*)"$/
     */
    public function iSeeTotalContentTypeGroup( $total, $identifier );

    /**
     * @Then /^I see the following Content Type Groups:$/
     */
    public function iSeeTheFollowingContentTypeGroups( TableNode $groups );
}
