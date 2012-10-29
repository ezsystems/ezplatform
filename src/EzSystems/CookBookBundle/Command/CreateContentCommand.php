<?php
/**
 * File containing the CreateContentCommand class.
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

class CreateContentCommand extends ContainerAwareCommand
{
    /**
     * This method override configures on input argument for the content id
     */
    protected function configure()
    {
        $this->setName( 'ezp_cookbook:createcontent' )->setDefinition(
                array(
                        new InputArgument( 'parentLocationId', InputArgument::REQUIRED, 'An existing parent location (node) id' ),
                        new InputArgument( 'contentType', InputArgument::REQUIRED, 'An existing content type identifier - the content type must contain a title field and a body field' ),
                        new InputArgument( 'title' , InputArgument::REQUIRED, 'the title of the content'),
                        new InputArgument( 'body' , InputArgument::REQUIRED, 'the body of the content')
                )
        );
    }

    /**
     * execute the command
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        // fetch the location argument
        $parentLocationId = $input->getArgument( 'parentLocationId' );

        // fetch the location argument
        $contentTypeIdentifier = $input->getArgument( 'contentType' );

        // fetch the title argument
        $title = $input->getArgument( 'title' );

        // fetch the body argument
        $body = $input->getArgument( 'body' );

        // get the repository from the di container
        $repository = $this->getContainer()->get( 'ezpublish.api.repository' );

        // get the content service from the repsitory
        $contentService = $repository->getContentService();

        // get the location service from the repsitory
        $locationService = $repository->getLocationService();

        // get the user service from the repsitory
        $userService = $repository->getUserService();

        // load admin user
        $user = $userService->loadUser(14);

        // set current user to admin
        $repository->setCurrentUser($user);

        // get the content type service from the repsitory
        $contentTypeService = $repository->getContentTypeService();

        try
        {
            // load the content type with identifier
            $contentType = $contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);

            // instanciate a location create struct
            $locationCreateStruct = $locationService->newLocationCreateStruct($parentLocationId);

            // instanciate a content creation struct
            $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');

            // set title field
            $contentCreateStruct->setField('title',$title);

            // set body field
            $contentCreateStruct->setField('body', $body);

            // create a draft using the content and location create structs
            $draft = $contentService->createContent($contentCreateStruct,array($locationCreateStruct));

            // publish the content draft
            $content = $contentService->publishVersion($draft->versionInfo);

            // print out the content
            print_r($content);
        }
        catch(\eZ\Publish\API\Repository\Exceptions\NotFoundException $e)
        {
            // react on content type or location not found
            $output->writeln($e->getMessage());
        }
        catch(\eZ\Publish\API\Repository\Exceptions\InvalidArgumentException $e)
        {
            // react on remote id exists already
            $output->writeln($e->getMessage());
        }
        catch(\eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException $e)
        {
            // react on a field is not valid
            $output->writeln($e->getMessage());
        }
        catch(\eZ\Publish\API\Repository\Exceptions\ContentValidationException $e)
        {
            // react on a required field is missing or empty
            $output->writeln($e->getMessage());
        }
    }
}
