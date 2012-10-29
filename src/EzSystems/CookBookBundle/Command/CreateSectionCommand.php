<?php
/**
 * File containing the ContentCreateCommand class.
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
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\ContentService;
//use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;

class CreateSectionCommand {

    protected function configure()
    {
        $this->setName( 'ezp_cookbook:createsection' )->setDefinition(
        array(
            new InputArgument( 'section_identifier', InputArgument::REQUIRED, 'a section identifier' ),
            new InputArgument( 'section_name', InputArgument::REQUIRED, 'a section name' )
        )
        );
    }
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $sectionIdentifier = $input->getArgument( 'section_identifier' );
        $sectionName = $input->getArgument( 'section_name' );

        $repository = $this->getContainer()->get( 'ezpublish.api.repository' );

        $contentService = $repository->getContentService();
        $sectionService = $repository->getSectionService();
        $userService = $repository->getUserService();
        $user = $userService->loadUser(14);
        $this->repository->setCurrentUser($user);
        // start a transaction
        $this->repository->beginTransaction();
        try
        {

        }
        catch( \Exception $e )
        {
            $output->writeln($e->getMessage());
            $this->repository->rollback();
        }
    }
}
