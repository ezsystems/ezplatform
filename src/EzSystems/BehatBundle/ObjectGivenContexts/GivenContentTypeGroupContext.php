<?php
/**
 * File containing the ContentTypeGroup class.
 *
 * This class contains the given steps for manipulating content type groups
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\ObjectGivenContexts;

use EzSystems\BehatBundle\ObjectGivenContexts\GivenContexts;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Behat\Gherkin\Node\TableNode;

class GivenContentTypeGroupContext extends GivenContexts
{
    /**
     * @Given /^I have (?:a |)Content Type Group with identifier "(?P<identifier>[^"]*)"$/
     */
    public function iHaveContentTypeGroup( $identifier )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getMainContext()->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        // verify if the content type group exists
        try
        {
            $contentTypeService->loadContentTypeGroupByIdentifier( $identifier );
        }
        // other wise create it
        catch ( NotFoundException $e )
        {
            $repository->sudo(
                function() use( $identifier, $contentTypeService )
                {
                    $ContentTypeGroupCreateStruct = $contentTypeService->newContentTypeGroupCreateStruct( $identifier );
                    return $contentTypeService->createContentTypeGroup( $ContentTypeGroupCreateStruct );
                }
            );
        }
    }

    /**
     * @Given /^I (?:do not|don\'t) have a Content Type Group with identifier "(?P<identifier>[^"]*)"$/
     */
    public function iDonTHaveContentTypeGroup( $identifier )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getMainContext()->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        // attempt to delete the content type group with the identifier
        try
        {
            $repository->sudo(
                function() use( $identifier, $contentTypeService )
                {
                    $contentTypeService->deleteContentTypeGroup(
                        $contentTypeService->loadContentTypeGroupByIdentifier( $identifier )
                    );
                }
            );
        }
        // other wise do nothing
        catch ( NotFoundException $e )
        {
            // needed for CS
        }
    }

    /**
     * @Given /^I have (?:the |)following ContentTypeGroups(?:\:|)$/
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
}
