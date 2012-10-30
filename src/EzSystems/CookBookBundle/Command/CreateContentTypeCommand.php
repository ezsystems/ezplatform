<?php
/**
 * File containing the CreateContentTypeCommand class.
 *
 * @copyright Copyright (C) 2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */
namespace EzSystems\CookBookBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\ContentService;
//use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;

class CreateContentTypeCommand extends ContainerAwareCommand
{

    /**
     * Add an input argument for the identifier for the group where the content type should be created
     * and an identifier for the content type
     */
    protected function configure()
    {
        $this->setName( 'ezp_cookbook:createcontenttype' )->setDefinition(
                array(
                        new InputArgument( 'content_type_group_identifier', InputArgument::REQUIRED, 'a content type group identifier' ),
                        new InputArgument( 'content_type_identifier', InputArgument::REQUIRED, 'a content type identifier' )
                )
        );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        // fetch command line arguments
        $groupIdentifier = $input->getArgument( 'content_type_group_identifier' );
        $contentTypeIdentifier = $input->getArgument( 'content_type_identifier' );

        // get the repository from the di container
        $repository = $this->getContainer()->get( 'ezpublish.api.repository' );

        // set the current user to admin
        $userService = $repository->getUserService();
        $repository->setCurrentUser($userService->loadUser(14));

        // get content type service from repository
        $contentTypeService = $repository->getContentTypeService();

        // load the content type group
        try
        {
            $contentTypeGroup = $contentTypeService->loadContentTypeGroupByIdentifier($groupIdentifier);
        }
        catch(\eZ\Publish\API\Repository\Exceptions\NotFoundException $e)
        {
            $output->writeln("content type group with identifier $groupIdentifier not found");
            return;
        }

        // instanciate a ContentTypeCreateStruct with the given content type identifier
        $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct($contentTypeIdentifier );
        // the main language code for names and description
        $contentTypeCreateStruct->mainLanguageCode = 'eng-GB';
        // the name schema for generating the content name by using the title attribute
        $contentTypeCreateStruct->nameSchema = '<title>';
        // set names for the content type
        $contentTypeCreateStruct->names = array(
                'eng-GB' => $contentTypeIdentifier . 'eng-GB',
                'ger-DE' => $contentTypeIdentifier . 'ger-DE',
        );
        // set description for the content type
        $contentTypeCreateStruct->descriptions = array(
                'eng-GB' => 'Description for ' . $contentTypeIdentifier . 'eng-GB',
                'ger-DE' => 'Description for ' . $contentTypeIdentifier . 'ger-DE',
        );

        /********************** add fields ***************************************/

        // add a title field
        $titleFieldCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct('title', 'ezstring');
        // set names and description for display
        $titleFieldCreateStruct->names = array('eng-GB' => 'Title','ger-DE' => 'Titel',);
        $titleFieldCreateStruct->descriptions = array('eng-GB' => 'The Title','ger-DE' => 'Der Titel');
        // set an group for the field
        $titleFieldCreateStruct->fieldGroup = 'content';
        // set position inside the content type
        $titleFieldCreateStruct->position = 10;
        // enable translation
        $titleFieldCreateStruct->isTranslatable = true;
        // require this field to set on content creation
        $titleFieldCreateStruct->isRequired = true;
        // enabled to find field via content search
        $titleFieldCreateStruct->isSearchable = true;

        // add field definition to content create struct
        $contentTypeCreateStruct->addFieldDefinition( $titleFieldCreateStruct );

        // add a body field
        $bodyFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('body', 'ezstring');
        // set names and description for display
        $bodyFieldCreate->names = array('eng-GB' => 'Body','ger-DE' => 'Text');
        $bodyFieldCreate->descriptions = array('eng-GB' => 'Description for Body','ger-DE' => 'Beschreibung Text');
        $bodyFieldCreate->fieldGroup = 'content';
        $bodyFieldCreate->position = 20;
        $bodyFieldCreate->isTranslatable = true;
        $bodyFieldCreate->isRequired = true;
        $bodyFieldCreate->isSearchable = true;

        // add field definition to content create struct
        $contentTypeCreateStruct->addFieldDefinition( $bodyFieldCreate );

        // set the content type group for the content type
        $groups = array($contentTypeGroup);

        // start a transaction
        $repository->beginTransaction();
        try
        {
            // create the content type - the returned content type is in status DRAFT
            $contentTypeDraft = $contentTypeService->createContentType($contentTypeCreateStruct,$groups);

            // publish the content type draft
            $contentTypeService->publishContentTypeDraft($contentTypeDraft);

            // commit the transaction
            $repository->commit();
        }
        catch( \eZ\Publish\API\Repository\Exceptions\UnauthorizedException $e )
        {
            // react on permission denied
            $output->writeln($e->getMessage());
            $repository->rollback();
        }
        catch( \eZ\Publish\API\Repository\Exceptions\ForbiddenException $e )
        {
            // react on identifier already exists
            $output->writeln($e->getMessage());
        }
        catch( \Exception $e )
        {
            $output->writeln($e->getMessage());
            $repository->rollback();
        }
    }
}

