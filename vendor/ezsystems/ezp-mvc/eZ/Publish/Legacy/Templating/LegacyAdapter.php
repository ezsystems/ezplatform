<?php
/**
 * File containing the LegacyAdapter class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Legacy\Templating;

/**
 * Generic legacy compatible object.
 * Aggregates any object that needs to be passed to an eZ Publish legacy template.
 */
class LegacyAdapter implements LegacyCompatible
{
    /**
     * Original object that is passed to the legacy template.
     *
     * @var mixed
     */
    private $object;

    /**
     * Properties that are available as public in aggregated object and that will be exposed as valid attributes.
     *
     * @var array
     */
    private $properties;

    /**
     * Public getters in aggregated object that will be exposed as valid attributes.
     *
     * @var array
     */
    private $getters;

    /**
     * @param mixed $transferredObject Object being passed to the legacy template.
     */
    public function __construct( $transferredObject )
    {
        $this->object = $transferredObject;

        // Registering available public properties
        $this->properties = array_map(
            function ()
            {
                return true;
            },
            get_object_vars( $transferredObject )
        );

        // Registering available getters
        $this->getters = array_fill_keys(
            array_filter(
                get_class_methods( $transferredObject ),
                function ( $method )
                {
                    return strpos( $method, 'get' ) === 0;
                }
            ),
            true
        );
    }

    /**
     * Returns true if object supports attribute $name
     *
     * @param string $name
     * @return bool
     */
    public function hasAttribute( $name )
    {
        if (
            isset( $this->properties[$name] )
            || isset( $this->getters['get' . ucfirst( $name )] )
        )
        {
            return true;
        }

        return false;
    }

    /**
     * Returns the value of attribute $name.
     *
     * @param string $name
     * @return mixed
     * @throws \InvalidArgumentException If $name is not supported by aggregated object
     */
    public function attribute( $name )
    {
        if ( isset( $this->properties[$name] ) )
            return $this->object->$name;

        $getterName = 'get' . ucfirst( $name );
        if ( isset( $this->getters[$getterName] ) )
            return $this->object->$getterName();

        throw new \InvalidArgumentException( "Unsupported attribute '$name' for " . get_class( $this->object ) );
    }

    /**
     * Returns an array of supported attributes (only their names).
     *
     * @return array
     */
    public function attributes()
    {
        $getters = $this->getters;
        array_walk(
            $getters,
            function ( $methodName )
            {
                return lcfirst( substr( $methodName, 3 ) );
            }
        );

        return array_keys( $this->properties + $getters );
    }
}
