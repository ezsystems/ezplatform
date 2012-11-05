<?php
/**
 * File containing the AddRelationCommand class.
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

class AddRelationCommand extends ContainerAwareCommand
{
    /**
     * This method override configures on input argument for the content id
     */
    protected function configure()
    {
        $this->setName( 'ezp_cookbook:addrelation' )->setDefinition(
                array(
                        new InputArgument( 'srcContentId' , InputArgument::REQUIRED, 'the source content'),
                        new InputArgument( 'destContentId' , InputArgument::REQUIRED, 'the destination content'),
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
        $srcContentId = $input->getArgument( 'srcContentId' );

        // fetch the title argument
        $destContentId = $input->getArgument( 'destContentId' );

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
            // load the src content info for the given id
            $srcContentInfo = $contentService->loadContentInfo($srcContentId);

            // load the content info for the given destination for the relation
            $destContentInfo = $contentService->loadContentInfo($destContentId);

            // create a draft from the current published version
            $contentDraft = $contentService->createContentDraft($srcContentInfo);

            // add a relation to the draft
            $contentService->addRelation($contentDraft->versionInfo, $destContentInfo);

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
