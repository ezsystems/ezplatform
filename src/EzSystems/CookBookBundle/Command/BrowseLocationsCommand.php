<?php
/**
 * File containing the BrowseLocationsCommand class.
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

class BrowseLocationsCommand extends ContainerAwareCommand
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
        $this->setName( 'ezp_cookbook:browseLocations' )->setDefinition(
                array(
                        new InputArgument( 'locationId', InputArgument::REQUIRED, 'An existing location id' )
                )
        );
    }

    /**
     * this method prints out the location name and calls this method recursive for the locations children
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param int $depth the current depth
     * @param OutputInterface $output
     */
    private function browseLocation(Location $location, $depth, OutputInterface $output) {

        // indent according to depth
        for($k=0;$k<$depth;$k++)
        {
            $output->write(' ');
        }

        // write the content name
        $output->writeln($location->contentInfo->name);

        // get location children
        $children = $this->locationService->loadLocationChildren($location);

        // browse children
        foreach($children as $childLocation)
        {
            $this->browseLocation($childLocation, $depth +1,$output);
        }
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

        // get the content service from the repsitory
        $this->contentService = $repository->getContentService();

        // get the location service from the repsitory
        $this->locationService = $repository->getLocationService();

        try
        {
            $location = $this->locationService->loadLocation($locationId);
            $this->browseLocation($location,0,$output);
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


