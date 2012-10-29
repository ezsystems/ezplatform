<?php
/**
 * File containing the FindContentCommand class.
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

class FindContentCommand extends ContainerAwareCommand
{
    /**
     * This method override configures on input argument for the content id
     */
    protected function configure()
    {
        $this->setName( 'ezp_cookbook:find' )->setDefinition(
                array(
                        new InputArgument( 'text', InputArgument::REQUIRED, 'text to search' )
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
        $text = $input->getArgument( 'text' );

        // get the repository from the di container
        $repository = $this->getContainer()->get( 'ezpublish.api.repository' );

        // get the search service
        $searchService = $repository->getSearchService();

        // create a new query object
        $query = new \eZ\Publish\API\Repository\Values\Content\Query();

        // add a fulltext criterion
        $query->criterion = new \eZ\Publish\API\Repository\Values\Content\Query\Criterion\Fulltext($text);

        // call findContent
        $result = $searchService->findContent($query);

        // print the total count of the search hits
        $output->writeln('Found ' . $result->totalCount . ' items');

        // iterate over the search hits
        foreach( $result->searchHits as $searchHit ) {
            // print out the content name
            $output->writeln($searchHit->valueObject->contentInfo->name);
        }
    }
}
