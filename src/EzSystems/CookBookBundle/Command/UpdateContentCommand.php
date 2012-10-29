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

class UpdateContentCommand extends ContainerAwareCommand
{
    /**
     * This method override configures on input argument for the content id
     */
    protected function configure()
    {
        $this->setName( 'ezp_cookbook:updatecontent' )->setDefinition(
                array(
                        new InputArgument( 'contentId' , InputArgument::REQUIRED, 'the content to be updated'),
                        new InputArgument( 'newtitle' , InputArgument::REQUIRED, 'the new title of the content'),
                        new InputArgument( 'newbody' , InputArgument::REQUIRED, 'the new body of the content')
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
        $contentId = $input->getArgument( 'contentId' );

        // fetch the title argument
        $newtitle = $input->getArgument( 'newtitle' );

        // fetch the body argument
        $newbody = $input->getArgument( 'newbody' );

        // get the repository from the di container
        $repository = $this->getContainer()->get( 'ezpublish.api.repository' );

        // get the content service from the repsitory
        $contentService = $repository->getContentService();

        // get the user service from the repsitory
        $userService = $repository->getUserService();

        // load admin user
        $user = $userService->loadUser(14);

        // set current user to admin
        $repository->setCurrentUser($user);

        try
        {
            // load the content info for the given id
            $contentInfo = $contentService->loadContentInfo($contentId);

            // create a draft from the current published version
            // an different version can be passed as second parameter
            $contentDraft = $contentService->createContentDraft($contentInfo);

            // instanciate a content update struct
            $contentUpdateStruct = $contentService->newContentUpdateStruct();

            // set language for new version
            $contentUpdateStruct->initialLanguageCode = 'eng-GB';

            // set fields
            $contentUpdateStruct->setField( 'title', $newtitle );
            $contentUpdateStruct->setField( 'body', $newbody );

            // update draft
            $contentDraft = $contentService->updateContent($contentDraft->versionInfo, $contentUpdateStruct);

            // publish draft
            $content = $contentService->publishVersion($contentDraft->versionInfo);

            print_r($content);
        }
        catch(\eZ\Publish\API\Repository\Exceptions\NotFoundException $e)
        {
            // react on content type not found
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
