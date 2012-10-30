<?php
/**
 * File containing the CookBookCommand class.
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

class ViewContentCommand extends ContainerAwareCommand
{
    /**
     * This method override configures on input argument for the content id
     */
    protected function configure()
    {
        $this->setName( 'ezp_cookbook:viewcontent' )->setDefinition(
            array(
                new InputArgument( 'contentId', InputArgument::REQUIRED, 'An existing content id' )
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
        // fetch the input argument
        $contentId = $input->getArgument( 'contentId' );

        // get the repository from the di container
        $repository = $this->getContainer()->get( 'ezpublish.api.repository' );

        // get the content service from the repsitory
        $contentService = $repository->getContentService();

        // get the field type service
        $fieldTypeService = $repository->getFieldTypeService();

        try
        {
            // load the content including all fields
            $content = $contentService->loadContent($contentId);

            // load the content type
            $contentType = $content->contentType;

            // iterate over field definitions
            foreach($contentType->fieldDefinitions as $fieldDefinition ) {
                // ignore ezpage
                if($fieldDefinition->fieldTypeIdentifier == 'ezpage') continue;

                // get the field type
                $fieldType = $fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier);

                // write field definition identifier
                $output->write($fieldDefinition->identifier . ": ");

                // use the field type toHash function to get a readable representation of the value
                $output->writeln($fieldType->toHash($content->getField($fieldDefinition->identifier)->value));
            }

        }
        catch( \eZ\Publish\API\Repository\Exceptions\NotFoundException $e )
        {
            // if the id is not found
            $output->writeln( "No content with id $contentId" );
        }
        catch( \eZ\Publish\API\Repository\Exceptions\UnauthorizedException $e )
        {
            // not allowed to read this content
            $output->writeln( "Anonymous users are not allowed to read content with id $contentId" );
        }
    }
}


