<?php
/**
 * File containing the MultipleObjectConverter class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Legacy\Templating\Converter;

/**
 * Interface for multiple object converters.
 * This is useful if one needs to convert several objects at once.
 */
interface MultipleObjectConverter extends ObjectConverter
{
    /**
     * Registers an object to the converter.
     * $alias is the variable name that will be exposed in the legacy template.
     *
     * @abstract
     * @param mixed $object
     * @param string $alias
     * @return void
     * @throws \InvalidArgumentException If $object is not an object
     */
    public function register( $object, $alias );

    /**
     * Converts all registered objects and returns them in a hash where the object's alias is the key.
     *
     * @abstract
     * @return array|\eZ\Publish\Legacy\Templating\LegacyCompatible[]
     */
    public function convertAll();
}
