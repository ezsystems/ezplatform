<?php
/**
 * File containing the CreateContentTypeGroupCommand class.
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
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Exceptions\ForbiddenException;

class CreateContentTypeGroupCommand extends ContainerAwareCommand
{

    /**
     * Add an input argument for the identifier for the new group
     */
    protected function configure()
    {
        $this->setName( 'ezp_cookbook:createcontenttypegroup' )->setDefinition(
            array(
                new InputArgument( 'content_type_group_identifier', InputArgument::REQUIRED, 'a content type group identifier' ),
            )
        );
    }

    /**
     * Executes create group command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $repository = $this->getContainer()->get( 'ezpublish.api.repository' );

        $repository->setCurrentUser(
            $repository->getUserService()->loadUserByCredentials( "admin", "admin" )
        );

        try
        {
            // instanciate a create struct
            $groupCreate = $this->contentTypeService->newContentTypeGroupCreateStruct(
                $input->getArgument( 'content_type_group_identifier' )
            );
            // call service method
            $contentTypeGroup =  $this->contentTypeService->createContentTypeGroup( $groupCreate );
            // print out the group
            print_r( $contentTypeGroup );
        }
        catch ( UnauthorizedException $e )
        {
            // react on permission denied
            $output->writeln( $e->getMessage() );
        }
        catch ( ForbiddenException $e )
        {
            // react on identifier already exists
            $output->writeln( $e->getMessage() );
        }
    }
}

