<?php
/**
 * File containing the CopyContentCommand class.
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

class CopyContentCommand extends ContainerAwareCommand
{
    /**
     * This method override configures on input argument for the content id
     */
    protected function configure()
    {
        $this->setName( 'ezp_cookbook:copycontent' )->setDefinition(
                array(
                        new InputArgument( 'contentId', InputArgument::REQUIRED, 'An existing content id' ),
                        new InputArgument( 'parentLocationId', InputArgument::REQUIRED, 'An existing parent location (node) id' ),
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
        $contentId = $input->getArgument( 'contentId' );

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


        try
        {

            // instanciate a location create struct
            $locationCreateStruct = $locationService->newLocationCreateStruct($parentLocationId);

            // load the content info from the given content id
            $contentInfo = $contentService->loadContentInfo($contentId);

            // copy the content - all versions are also copied. If only a specific version
            // should be copied it can be passed as third parameter
            // NOTE: the children are not copied wit this method - use LocationService::copySubtree instead
            $copiedContent = $contentService->copyContent($contentInfo, $locationCreateStruct);

            // print out the content
            print_r($copiedContent);
        }
        catch(\eZ\Publish\API\Repository\Exceptions\NotFoundException $e)
        {
            // react on content not found
            $output->writeln($e->getMessage());
        }
        catch(\eZ\Publish\API\Repository\Exceptions\UnauthorizedException $e)
        {
            // react on permission denied
            $output->writeln($e->getMessage());
        }

    }
}

