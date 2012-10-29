<?php
/**
 * File containing the DeleteSubtree class.
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
use eZ\Publish\API\Repository\Values\Content\Location;

class DeleteSubtree extends ContainerAwareCommand
{
    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    private $locationService;

    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;


    /**
     * This method override configures on input argument for the content id
     */
    protected function configure()
    {
        $this->setName( 'ezp_cookbook:deletesubtree' )->setDefinition(
                array(
                        new InputArgument( 'locationId', InputArgument::REQUIRED, 'An existing location id' )
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
        $locationId = $input->getArgument( 'locationId' );

        // get the repository from the di container
        $repository = $this->getContainer()->get( 'ezpublish.api.repository' );

        // get the user service from the repsitory
        $userService = $repository->getUserService();

        // load admin user
        $user = $userService->loadUser(14);

        // set current user to admin
        $repository->setCurrentUser($user);

        // get the location service from the repsitory
        $this->locationService = $repository->getLocationService();

        try
        {
            // load the location from the given id
            $location = $locationService->loadLocation($locationId);

            // delete location (permanently)
            $locationService->deleteLocation($locationId);
        }
        catch( \eZ\Publish\API\Repository\Exceptions\NotFoundException $e )
        {
            // if the location id was not found
            $output->writeln( "No content with id $locationId" );
        }
        catch( \eZ\Publish\API\Repository\Exceptions\UnauthorizedException $e )
        {
            // not allowed to read this location
            $output->writeln( "Anonymous users are not allowed to read location with id $locationId" );
        }
    }
}


