<?php
/**
 * File containing the GivenContexts class.
 *
 * This is the parent object of all object given steps sub contexts
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\ObjectGivenContexts;

use Behat\Behat\Context\BehatContext;
use eZ\Publish\API\Repository\Values\ValueObject;

abstract class GivenContexts extends BehatContext
{
    /**
     * This var is needed to be set when the __destruct is called
     * (if any object was created)
     *
     * @var eZ\Publish\API\Repository\Repository
     */
    private $repository;

    /**
     * This is the var that will be used by __destruct
     *
     * @var array
     */
    protected $createdObjects = array();

    /**
     * This method is actually needed for the deletion of the created objects
     *
     * @return eZ\Publish\API\Repository\Repository
     *
     * @see $this->repository
     */
    protected function getRepository()
    {
        if ( empty( $this->repository ) )
        {
            $this->repository = $this->getMainContext()->getRepository();
        }

        return $this->repository;
    }

    /**
     * Destroy/remove/delete all created objects (from given steps)
     */
    public function __destruct()
    {
        foreach ( $this->createdObjects as $object )
        {
            $this->destroy( $object );
        }
    }

    /**
     * This is used by the __destruct() function to delete/remove all the objects
     * that were created for testing
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $object Object that should be destroyed/removed
     */
    abstract protected function destroy( ValueObject $object );
}
