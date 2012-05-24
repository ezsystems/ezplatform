<?php
/**
 * File containing the RepositoryFactory class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishCoreBundle\ApiLoader;

use eZ\Publish\SPI\Persistence\Handler as PersistenceHandler;
use eZ\Publish\SPI\IO\Handler as IoHandler;
use eZ\Publish\SPI\FieldType\FieldType as FieldTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RepositoryFactory
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * Collection of fieldTypes, lazy loaded via a closure
     *
     * @var \Closure[]
     */
    protected $fieldTypes;

    public function __construct( ContainerInterface $container )
    {
        $this->container = $container;
    }

    /**
     * Builds the main repository, heart of eZ Publish API
     *
     * @param \eZ\Publish\SPI\Persistence\Handler $persistenceHandler
     * @param \eZ\Publish\SPI\IO\Handler $ioHandler
     * @return \eZ\Publish\API\Repository\Repository
     */
    public function buildRepository( PersistenceHandler $persistenceHandler, IoHandler $ioHandler )
    {
        $repositoryClass = $this->container->getParameter( 'ezpublish.api.repository.class' );
        return new $repositoryClass(
            $persistenceHandler,
            $ioHandler,
            array(
                 'contentType'      => $this->getContentTypeSettings()
            )
        );
    }

    /**
     * Registers an eZ Publish field type.
     * Field types are being registered as a closure so that they will be lazy loaded.
     *
     * @param string $fieldTypeServiceId The field type service Id
     * @param string $fieldTypeAlias The field type alias (e.g. "ezstring")
     */
    public function registerFieldType( $fieldTypeServiceId, $fieldTypeAlias )
    {
        $container = $this->container;
        $this->fieldTypes[$fieldTypeAlias] = function() use ( $container, $fieldTypeServiceId )
        {
            return $container->get( $fieldTypeServiceId );
        };
    }

    /**
     * Returns content type settings
     *
     * @return array
     */
    protected function getContentTypeSettings()
    {
        return array(
            'field_type'        => $this->fieldTypes
        );
    }
}
