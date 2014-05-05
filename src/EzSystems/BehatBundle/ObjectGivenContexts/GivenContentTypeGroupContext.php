<?php
/**
 * File containing the ContentTypeGroup class.
 *
 * This class contains the given steps for manipulating content type groups
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\ObjectGivenContexts;

use EzSystems\BehatBundle\ObjectGivenContexts\GivenContexts;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_Assert as Assertion;

class GivenContentTypeGroupContext extends GivenContexts
{
    /**
     * @Given /^I have (?:a |)Content Type Group with identifier "(?P<identifier>[^"]*)"$/
     */
    public function iHaveContentTypeGroup( $identifier )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $newContentTypeGroup = $repository->sudo(
            function() use( $identifier, $contentTypeService )
            {
                // verify if the content type group exists
                try
                {
                    $contentTypeService->loadContentTypeGroupByIdentifier( $identifier );
                    return null;
                }
                // other wise create it
                catch ( NotFoundException $e )
                {
                    $ContentTypeGroupCreateStruct = $contentTypeService->newContentTypeGroupCreateStruct( $identifier );
                    return $contentTypeService->createContentTypeGroup( $ContentTypeGroupCreateStruct );
                }
            }
        );

        if ( !empty( $newContentTypeGroup ) )
        {
            $this->createdObjects[] = $newContentTypeGroup;
        }
    }

    /**
     * @Given /^I (?:do not|don\'t) have a Content Type Group with identifier "(?P<identifier>[^"]*)"$/
     */
    public function iDonTHaveContentTypeGroup( $identifier )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $repository->sudo(
            function() use( $identifier, $contentTypeService )
            {
                // attempt to delete the content type group with the identifier
                try
                {
                    $contentTypeService->deleteContentTypeGroup(
                        $contentTypeService->loadContentTypeGroupByIdentifier( $identifier )
                    );
                }
                // other wise do nothing
                catch ( NotFoundException $e )
                {
                    // nothing to do
                }
            }
        );
    }

    /**
     * @Given /^I have (?:the |)following Content Type Groups(?:\:|)$/
     */
    public function iHaveTheFollowingContentTypeGroups( TableNode $table )
    {
        $groups = $table->getNumeratedRows();

        array_shift( $groups );
        foreach ( $groups as $group )
        {
            $this->iHaveContentTypeGroup( $group[0] );
        }
    }

    /**
     * This is used by the __destruct() function to delete/remove all the objects
     * that were created for testing
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $object Object that should be destroyed/removed
     */
    protected function destroy( ValueObject $object )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();

        $repository->sudo(
            function() use( $repository, $object )
            {
                try
                {
                    $contentTypeService = $repository->getContentTypeService();
                    $contentTypeService->deleteContentTypeGroup( $contentTypeService->loadContentTypeGroup( $object->id ) );
                }
                catch ( NotFoundException $e )
                {
                    // nothing to do
                }
            }
        );
    }
}
