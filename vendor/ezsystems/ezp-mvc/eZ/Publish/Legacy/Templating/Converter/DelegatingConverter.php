<?php
/**
 * File containing the DelegatingConverter class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Legacy\Templating\Converter;

class DelegatingConverter implements MultipleObjectConverter
{
    /**
     * @var \eZ\Publish\Legacy\Templating\Converter\ObjectConverter[]
     */
    private $convertersMap;

    /**
     * Generic converter.
     * Will be used if no converter has been register for an object.
     *
     * @var \eZ\Publish\Legacy\Templating\Converter\ObjectConverter
     */
    private $genericConverter;

    /**
     * Array of objects to convert, indexed by their alias (variable name in legacy templates)
     *
     * @var array
     */
    private $objectsToConvert;

    public function __construct( ObjectConverter $genericConverter )
    {
        $this->convertersMap = array();
        $this->objectsToConvert = array();
        $this->genericConverter = $genericConverter;
    }

    /**
     * Registers $converter for classes contained in $classes
     *
     * @param \eZ\Publish\Legacy\Templating\Converter\ObjectConverter $converter
     * @param $class Class the converter is for
     */
    public function addConverter( ObjectConverter $converter, $class )
    {
        $this->convertersMap[$class] = $converter;
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
        $this->objectsToConvert[$alias] = $object;
    }

    /**
     * Converts all registered objects and returns them in a hash where the object's alias is the key.
     *
     * @return array|\eZ\Publish\Legacy\Templating\LegacyCompatible[]
     */
    public function convertAll()
    {
        $convertedObjects = array();
        $delegatingConverters = array();

        foreach ( $this->objectsToConvert as $alias => $obj )
        {
            $className = get_class( $obj );
            if ( isset( $this->convertersMap[$className] ) )
            {
                $converter = $this->convertersMap[$className];
                // MultipleObjectConverter => Register it for later conversion
                if ( $converter instanceof MultipleObjectConverter )
                {
                    $converter->register( $obj, $alias );
                    $delegatingConverters[] = $converter;
                }
                else
                {
                    $convertedObjects[$alias] = $converter->convert( $obj );
                }
            }
            // No registered converter => fallback to generic converter
            else
            {
                $convertedObjects[$alias] = $this->genericConverter->convert( $obj );
            }
        }

        // Finally loop against delegating converters (aka MultipleObjectConverter instances) to convert all registered objects
        foreach ( $delegatingConverters as $converter )
        {
            $convertedObjects += $converter->convertAll();
        }

        return $convertedObjects;
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
        $className = get_class( $object );
        if ( isset( $this->convertersMap[$className] ) )
            $converter = $this->convertersMap[$className];
        else
            $converter = $this->genericConverter;

        return $converter->convert( $object );
    }
}
