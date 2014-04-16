<?php
/**
 * File containing the AuthenticationContext class.
 *
 * This class contains the implementation of the Authentication interface which
 * has the sentences for the Authentication BDD
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Features\Context\Browser\SubContexts;

use EzSystems\BehatBundle\Features\Context\SentencesInterfaces\Authentication;
use Behat\Behat\Exception\PendingException;
use Behat\Behat\Context\Step;

/**
 * AuthenticationContext
 */
class AuthenticationContext extends BrowserSubContext implements Authentication
{
    public function iAmLoggedInAsAn( $role )
    {
        switch( strtolower( $role ) ) {
        case 'administrator':
            $user = 'admin';
            $passwd = 'publish';
            break;

        default:
            throw new PendingException( "Login with '$role' role not implemented yet" );
        }

        return $this->iAmLoggedInAsWithPassword( $user, $passwd );
    }

    public function iAmLoggedInAsWithPassword( $user, $password )
    {
        return array(
            new Step\Given( 'I am on "/login"' ),
            new Step\When( 'I fill in "Username" with "' . $user . '"' ),
            new Step\When( 'I fill in "Password" with "' . $password . '"' ),
            new Step\When( 'I press "Login"' ),
            new Step\Then( 'I should be on "/"' ),
        );
    }

    public function iAmNotLoggedIn()
    {
        return array(
            new Step\Given( 'I am on "/logout"' ),
            new Step\Then( 'I see "Homepage" page' ),
        );
    }
}
