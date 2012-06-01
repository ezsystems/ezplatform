<?php
/**
 * File containing the StorageEngineFactory class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishCoreBundle\ApiLoader;

use eZ\Bundle\EzPublishCoreBundle\ApiLoader\Exception\InvalidStorageEngine;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The storage engine factory.
 */
class StorageEngineFactory
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * Hash of registered storage engines.
     * Key is the storage engine identifier, value is its corresponding service Id
     *
     * @var array
     */
    protected $storageEngines = array();

    public function __construct( ContainerInterface $container )
    {
        $this->container = $container;
    }

    /**
     * Registers $storageEngineServiceId as a service Id to be used as a valid storage engine, with identifier $storageEngineIdentifier
     *
     * @param string $storageEngineServiceId
     * @param string $storageEngineIdentifier
     */
    public function registerStorageEngine( $storageEngineServiceId, $storageEngineIdentifier )
    {
        $this->storageEngines[$storageEngineIdentifier] = $storageEngineServiceId;
    }

    /**
     * Builds storage engine identified by $storageEngineIdentifier (the "alias" attribute in the service tag)
     *
     * @param string $storageEngineIdentifier The storage engine identifier
     * @return \eZ\Publish\SPI\Persistence\Handler
     * @throws \eZ\Bundle\EzPublishCoreBundle\ApiLoader\Exception\InvalidStorageEngine
     */
    public function buildStorageEngine( $storageEngineIdentifier )
    {
        if ( !isset( $this->storageEngines[$storageEngineIdentifier] ) )
        {
            throw new InvalidStorageEngine(
                "Invalid storage engine '$storageEngineIdentifier'. Could not find any service tagged as ezpublish.storageEngine with alias $storageEngineIdentifier."
            );
        }

        $serviceId = $this->storageEngines[$storageEngineIdentifier];
        return $this->container->get( $serviceId );
    }
}
