<?php
/**
 * File containing the LegacyStorageEngineFactory class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishCoreBundle\ApiLoader;

use eZ\Publish\Core\Persistence\Legacy\EzcDbHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LegacyStorageEngineFactory
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    protected $converters = array();

    protected $externalStorages = array();

    public function __construct( ContainerInterface $container )
    {
        $this->container = $container;
    }

    /**
     * Registers a field type converter as expected in legacy storage engine.
     * $className must implement eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter interface.
     *
     * @param string $typeIdentifier Field type identifier the converter will be used for
     * @param string $className FQN of the converter class
     */
    public function registerFieldTypeConverter( $typeIdentifier, $className )
    {
        $this->converters[$typeIdentifier] = $className;
    }

    /**
     * Registers an external storage handler for a field type.
     * $className must implement \eZ\Publish\SPI\Persistence\Fields\Storage interface.
     *
     * @param string $typeIdentifier Field type identifier the handler will be used for
     * @param $className FQN of the external storage handler class
     */
    public function registerFieldTypeExternalStorageHandler( $typeIdentifier, $className )
    {
        $this->externalStorages[$typeIdentifier] = $className;
    }

    /**
     * Builds the Legacy Storage Engine
     *
     * @param string $dsn <database_type>://<user>:<password>@<host>/<database_name>
     * @param $deferTypeUpdate
     * @param \eZ\Publish\Core\Persistence\Legacy\EzcDbHandler|null $dbHandler
     * @return \eZ\Publish\Core\Persistence\Legacy\Handler
     */
    public function buildLegacyEngine( $dsn, $deferTypeUpdate, EzcDbHandler $dbHandler = null )
    {
        $legacyEngineClass = $this->container->getParameter( 'ezpublish.api.storage_engine.legacy.class' );
        return new $legacyEngineClass(
            array(
                 'dsn'                          => $dsn,
                 'defer_type_update'            => (bool)$deferTypeUpdate,
                 'transformation_rule_files'    => array(),
                 'field_converter'              => $this->converters,
                 'external_storages'            => $this->externalStorages
            )
        );
    }
}
