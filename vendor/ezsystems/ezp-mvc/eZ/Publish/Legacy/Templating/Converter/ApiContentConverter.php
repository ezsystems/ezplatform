<?php
/**
 * File containing the ApiContentConverter class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Legacy\Templating\Converter;

use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZContentObject;
use eZContentObjectTreeNode;

class ApiContentConverter implements MultipleObjectConverter
{
    /**
     * @var \Closure
     */
    private $legacyKernelClosure;

    /**
     * Hash of API objects to be converted, indexed by alias.
     *
     * @var \eZ\Publish\API\Repository\Values\ValueObject[]
     */
    private $apiObjects;

    public function __construct( \Closure $legacyKernelClosure )
    {
        $this->legacyKernelClosure = $legacyKernelClosure;
        $this->apiObjects = array();
    }

    /**
     * @return \eZ\Publish\Legacy\Kernel
     */
    final protected function getLegacyKernel()
    {
        $closure = $this->legacyKernelClosure;
        return $closure();
    }

    /**
     * Converts $object to make it compatible with eZTemplate API.
     *
     * @param $object
     * @return mixed|\eZ\Publish\Legacy\Templating\LegacyCompatible
     * @throws \InvalidArgumentException If $object is actually not an object
     */
    public function convert( $object )
    {
        if ( !is_object( $object ) )
            throw new \InvalidArgumentException( 'Transferred object must be a real object. Got ' . gettype( $object ) );

        return $this->getLegacyKernel()->runCallback(
            function () use ( $object )
            {
                if ( $object instanceof Content )
                {
                    return eZContentObject::fetch( $object->getVersionInfo()->getContentInfo()->id );
                }
                else if ( $object instanceof Location )
                {
                    return eZContentObjectTreeNode::fetch( $object->id );
                }
            }
        );
    }

    /**
     * Registers an object to the converter.
     * $alias is the variable name that will be exposed in the legacy template.
     *
     * @param mixed $object
     * @param string $alias
     * @return void
     * @throws \InvalidArgumentException If $object is not an object
     */
    public function register( $object, $alias )
    {
        if ( !is_object( $object ) )
            throw new \InvalidArgumentException( 'Transferred object must be a real object. Got ' . gettype( $object ) );

        $this->apiObjects[$alias] = $object;
    }

    /**
     * Converts all registered objects and returns them in a hash where the object's alias is the key.
     *
     * @return array|\eZ\Publish\Legacy\Templating\LegacyCompatible[]
     */
    public function convertAll()
    {
        $apiObjects = $this->apiObjects;
        return $this->getLegacyKernel()->runCallback(
            function () use ( $apiObjects )
            {
                $convertedObjects = array();
                foreach ( $apiObjects as $alias => $apiObject )
                {
                    if ( $apiObject instanceof Content )
                    {
                        $convertedObjects[$alias] = eZContentObject::fetch( $apiObject->getVersionInfo()->getContentInfo()->id );
                    }
                    else if ( $apiObject instanceof Location )
                    {
                        $convertedObjects[$alias] = eZContentObjectTreeNode::fetch( $apiObject->id );
                    }
                }

                return $convertedObjects;
            }
        );
    }
}
