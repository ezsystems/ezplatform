<?php
/**
* File containing the Error class.
*
* This interface has the sentences definitions for the Error steps
*
* @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
* @version //autogentag//
*/

namespace EzSystems\BehatBundle\Features\Context\SentencesInterfaces;

/**
 * Errors Sentences Interface
 */
interface Error
{
    /**
     * @Then /^I see an invalid field error$/
     */
    public function iSeeAnInvalidFieldError();

    /**
     * @Then /^I see not authorized error$/
     */
    public function iSeeNotAuthorizedError();
}
