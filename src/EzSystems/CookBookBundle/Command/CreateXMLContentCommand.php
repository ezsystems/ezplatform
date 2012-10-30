<?php
/**
 * File containing the CreateXMLContentCommand class.
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

class CreateXMLContentCommand extends ContainerAwareCommand
{
    /**
     * This method overrides configure
     */
    protected function configure()
    {
        $this->setName( 'ezp_cookbook:createxmltext' )->setDefinition(
                array(
                        new InputArgument( 'parentLocationId', InputArgument::REQUIRED, 'An existing parent location (node) id' ),
                        new InputArgument( 'name' , InputArgument::REQUIRED, 'the name of the folder'),
                        new InputArgument( 'imageid' , InputArgument::REQUIRED, 'an id of a image content object')
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

        // fetch the name argument
        $name = $input->getArgument( 'name' );

        // fetch the image id
        $imageId = $input->getArgument( 'imageid' );

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
            $contentType = $contentTypeService->loadContentTypeByIdentifier("folder");

            // instanciate a location create struct
            $locationCreateStruct = $locationService->newLocationCreateStruct($parentLocationId);

            // instanciate a content creation struct
            $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');

            // set name field
            $contentCreateStruct->setField('name',$name);

            // contruct a xml text

            $xmltext = "<?xml version='1.0' encoding='utf-8'?><section xmlns:image='http://ez.no/namespaces/ezpublish3/image/' xmlns:xhtml='http://ez.no/namespaces/ezpublish3/xhtml/' xmlns:custom='http://ez.no/namespaces/ezpublish3/custom/'><paragraph>This is a <strong>image test</strong></paragraph><paragraph><embed view='embed' size='medium' object_id='$imageId' custom:offset='0' custom:limit='5'/></paragraph></section>";

            // add the xmltext field
            $contentCreateStruct->setField("description",$xmltext);

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
