<?php
/**
 * File containing the Authentication class.
 *
 * This interface as the sentences definitions for the authentication steps
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Features\Context\SentencesInterfaces;

/**
 * Authentication Sentences Interface
 */
interface Authentication
{
    /**
     * @Given /^I am not logged in$/
     */
    public function iAmNotLoggedIn();

    /**
     * @Given /^I am logged in as "(?P<user>[^"]*)" with password "(?P<password>[^"]*)"$/
     */
    public function iAmLoggedInAsWithPassword( $user, $password );

    /**
     * @Given /^I am logged (?:in |)as (?:an |a |)"(?P<role>[^"]*)"$/
     */
    public function iAmLoggedInAsAn( $role );
}

