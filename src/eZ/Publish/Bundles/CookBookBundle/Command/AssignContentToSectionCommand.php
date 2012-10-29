<?php
/**
 * File containing the AssignContentToSectionCommand class.
 *
 * @copyright Copyright (C) 2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */
namespace eZ\Publish\Bundles\CookBookBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class AssignContentToSectionCommand extends ContainerAwareCommand
{
    /**
     * This method override configures on input argument for the content id
     */
    protected function configure()
    {
        $this->setName( 'ezp_cookbook:assignsection' )->setDefinition(
                array(
                        new InputArgument( 'contentId', InputArgument::REQUIRED, 'An existing content id' ),
                        new InputArgument( 'sectionId', InputArgument::REQUIRED, 'An existing section id' ),
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
        $sectionId = $input->getArgument( 'sectionId' );

        // fetch the location argument
        $contentId = $input->getArgument( 'contentId' );

        // get the repository from the di container
        $repository = $this->getContainer()->get( 'ezpublish.api.repository' );

        // get the content service from the repsitory
        $contentService = $repository->getContentService();

        // get the section service from the repsitory
        $sectionService = $repository->getSectionService();

        // get the user service from the repsitory
        $userService = $repository->getUserService();

        // load admin user
        $user = $userService->loadUser(14);

        // set current user to admin
        $repository->setCurrentUser($user);


        try
        {
            // load the content info from the given content id
            $contentInfo = $contentService->loadContentInfo($contentId);

            // load the section
            $section = $sectionService->loadSection($sectionId);

            // assign the section to the content
            $sectionService->assignSection($contentInfo, $section);

            // realod an print out
            $contentInfo =  $contentService->loadContentInfo($contentId);
            $output->writeln($contentInfo->sectionId);
        }
        catch(\eZ\Publish\API\Repository\Exceptions\NotFoundException $e)
        {
            // react on content or section not found
            $output->writeln($e->getMessage());
        }
        catch(\eZ\Publish\API\Repository\Exceptions\UnauthorizedException $e)
        {
            // react on permission denied
            $output->writeln($e->getMessage());
        }

    }
}


