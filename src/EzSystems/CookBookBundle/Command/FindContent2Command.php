<?php
/**
 * File containing the FindContent2Command class.
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
use \eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;

class FindContent2Command extends ContainerAwareCommand
{
    /**
     * This method override configures on input argument for the content id
     */
    protected function configure()
    {
        $this->setName( 'ezp_cookbook:find2' )->setDefinition(
                array(
                        new InputArgument( 'text', InputArgument::REQUIRED, 'text to search in title field' ),
                        new InputArgument( 'contentTypeId', InputArgument::REQUIRED, 'content type id' ),
                        new InputArgument( 'locationId', InputArgument::REQUIRED, 'location id' ),
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
        //fetch the input argument
        $text = $input->getArgument( 'text' );

        // fetch the input argument
        $contentTypeId = $input->getArgument( 'contentTypeId' );

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

        // get the search service
        $searchService = $repository->getSearchService();

        $locationService = $repository->getLocationService();

        $location = $locationService->loadLocation($locationId);

        // create a new query object
        $query = new \eZ\Publish\API\Repository\Values\Content\Query();

        // create a full text criterion
        $criterion1 = new \eZ\Publish\API\Repository\Values\Content\Query\Criterion\FullText($text);

        // create a subtree criterion (restrict results to belong to the subtree)
        $criterion2 = new \eZ\Publish\API\Repository\Values\Content\Query\Criterion\Subtree($location->pathString);

        // create a content type criterion (restrict to the given content type)
        $criterion3 = new \eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeId($contentTypeId);

        // make a logical AND of the two criteria
        $query->criterion = new \eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAND(
                array($criterion1,$criterion2,$criterion3));

        // call findContent
        $result = $searchService->findContent($query);

        // print the total count of the search hits
        $output->writeln('Found ' . $result->totalCount . ' items');

        // iterate over the search hits
        foreach( $result->searchHits as $searchHit )
        {
            // print out the content name
            $output->writeln($searchHit->valueObject->contentInfo->name);
        }
    }
}

