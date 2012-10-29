<?php
/**
 * File containing the HideLocationCommand class.
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
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;

class HideLocationCommand extends ContainerAwareCommand
{
    /**
     * This method override configures on input argument for the content id
     */
    protected function configure()
    {
        $this->setName( 'ezp_cookbook:addlocation' )->setDefinition(
            array(
                new InputArgument( 'locationId', InputArgument::REQUIRED, 'An existing location id' ),
            )
        );
    }

    /**
     * Executes the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        // fetch the location argument
        $locationId = $input->getArgument( 'locationId' );

        // get the repository from the di container
        $repository = $this->getContainer()->get( 'ezpublish.api.repository' );

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
            // load the location info from the given location id
            $location = $contentService->loadContentInfo( $contentId );

            // hide the location
            $hiddenLocation = $locationService->hideLocation( $location );

            // print out the location
            print_r( $hiddenLocation );

            // unhide the location
            $unhiddenLocation = $locationService->unhideLocation( $hiddenLocation );

            // print out the location
            print_r( $unhiddenLocation );

        }
        catch ( NotFoundException $e )
        {
            // react on content or location not found
            $output->writeln( $e->getMessage() );
        }
        catch ( UnauthorizedException $e )
        {
            // react on permission denied
            $output->writeln( $e->getMessage() );
        }
    }
}
