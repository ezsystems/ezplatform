<?php
/**
* File containing the Error class.
*
* This interface has the sentences definitions for the Error steps
*
* @copyright Copyright (C) eZ Systems AS. All rights reserved.
* @license For full copyright and license information view LICENSE file distributed with this source code.
* @version //autogentag//
*/

namespace EzSystems\BehatBundle\Features\Context\SentencesInterfaces;

/**
 * Errors Sentences Interface
 */
interface Error
{
    /**
     * @Then /^I see (?:an |)invalid field error$/
     */
    public function iSeeAnInvalidFieldError();

    /**
     * @Then /^I see (?:a |)not authorized error$/
     * @Then /^I see (?:an |)unauthorized error$/
     */
    public function iSeeNotAuthorizedError();
}
