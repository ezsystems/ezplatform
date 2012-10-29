<?php
/**
 * File containing the SubtreeCommand class.
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

class SubtreeCommand extends ContainerAwareCommand
{
    /**
     * This method override configures on input argument for the content id
     */
    protected function configure()
    {
        $this->setName( 'ezp_cookbook:subtree' )->setDefinition(
                array(
                        new InputArgument( 'operation', InputArgument::REQUIRED, 'copy or move' ),
                        new InputArgument( 'locationId', InputArgument::REQUIRED, 'An existing location id' ),
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
        // fetch operation (copy or move)
        $operation = $input->getArgument('operation');

        // fetch the location argument
        $parentLocationId = $input->getArgument( 'parentLocationId' );

        // fetch the location argument
        $locationId = $input->getArgument( 'locationId' );

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
            // load the location from the given location id
            $location = $locationService->loadLocation($locationId);

            // load the parent location to move/copy to
            $parentLocation = $locationService->loadLocation($parentLocationId);

            if($operation == 'copy') {
                $newLocation = $locationService->copySubtree($location, $parentLocation);
            }
            else if($operation == 'move')
            {
                $newLocation = $locationService->moveSubtree($location, $parentLocation);
            }
            else
            {
                $output->writeln("operation must be copy or move");
                return;
            }

            print_r($newLocation);
        }
        catch(\eZ\Publish\API\Repository\Exceptions\NotFoundException $e)
        {
            // react on content or location not found
            $output->writeln($e->getMessage());
        }
        catch(\eZ\Publish\API\Repository\Exceptions\UnauthorizedException $e)
        {
            // react on permission denied
            $output->writeln($e->getMessage());
        }

    }
}


