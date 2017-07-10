<?php

namespace Kore\RatingFieldTypeBundle\Storage\FieldType;

use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\Core\FieldType\Value as CoreValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue;

class Type extends FieldType
{
    /**
     * Returns the field type identifier for this field type.
     *
     * This identifier should be globally unique and the implementer of a
     * FieldType must take care for the uniqueness. It is therefore recommended
     * to prefix the field-type identifier by a unique string that identifies
     * the implementer. A good identifier could for example take your companies
     * main domain name as a prefix in reverse order.
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        // @EXT: Necessary
        return 'koreRating';
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated
     * value object.
     *
     * If given $inputValue could not be converted or is already an instance of
     * dedicate value object, the method should simply return it.
     *
     * This is an operation method for {@see acceptValue()}.
     *
     * Example implementation:
     * <code>
     *  protected function createValueFromInput( $inputValue )
     *  {
     *      if ( is_array( $inputValue ) )
     *      {
     *          $inputValue = \eZ\Publish\Core\FieldType\CookieJar\Value( $inputValue );
     *      }
     *
     *      return $inputValue;
     *  }
     * </code>
     *
     * @param mixed $inputValue
     *
     * @return mixed The potentially converted input value.
     */
    protected function createValueFromInput($inputValue)
    {
        // @EXT: Default possible depending on value class, at least for
        // trivial values
        //
        // There is probably a connection with the edit template, which is
        // missing in the tutoorial.
        if (is_string($inputValue)) {
            $inputValue = new Value(['rating' => $inputValue]);
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * Note that this does not include validation after the rules
     * from validators, but only plausibility checks for the general data
     * format.
     *
     * This is an operation method for {@see acceptValue()}.
     *
     * Example implementation:
     * <code>
     *  protected function checkValueStructure( Value $value )
     *  {
     *      if ( !is_array( $value->cookies ) )
     *      {
     *          throw new InvalidArgumentException( "An array of assorted cookies was expected." );
     *      }
     *  }
     * </code>
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \eZ\Publish\Core\FieldType\Value $value
     */
    protected function checkValueStructure(CoreValue $value)
    {
        // @EXT: Default possible depending on value
        if (!$value->rating) {
            throw new eZ\Publish\Core\Base\Exceptions\InvalidArgumentType(
               '$value->rating',
               'number',
               $value->rating
           );
        }
    }

    /**
     * Returns the empty value for this field type.
     *
     * This value will be used, if no value was provided for a field of this
     * type and no default value was specified in the field definition. It is
     * also used to determine that a user intentionally (or unintentionally)
     * did not set a non-empty value.
     *
     * @return \eZ\Publish\SPI\FieldType\Value
     */
    public function getEmptyValue()
    {
        // @EXT: Default possible when we know about the value class
        return new Value();
    }

    /**
     * Returns a human readable string representation from the given $value.
     *
     * It will be used to generate content name and url alias if current field
     * is designated to be used in the content name/urlAlias pattern.
     *
     * The used $value can be assumed to be already accepted by {@link *
     * acceptValue()}.
     *
     * @deprecated Since 6.3/5.4.7, use \eZ\Publish\SPI\FieldType\Nameable
     * @param \eZ\Publish\SPI\FieldType\Value $value
     *
     * @return string
     */
    public function getName(SPIValue $value)
    {
        return (string) $value;
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     *
     * Return value is mixed. It should be something which is sensible for
     * sorting.
     *
     * It is up to the persistence implementation to handle those values.
     * Common string and integer values are safe.
     *
     * For the legacy storage it is up to the field converters to set this
     * value in either sort_key_string or sort_key_int.
     *
     * @param \eZ\Publish\Core\FieldType\Value $value
     *
     * @return mixed
     */
    protected function getSortInfo(CoreValue $value)
    {
        return $this->getName($value);
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * This is the reverse operation to {@link toHash()}. At least the hash
     * format generated by {@link toHash()} must be converted in reverse.
     * Additional formats might be supported in the rare case that this is
     * necessary. See the class description for more details on a hash format.
     *
     * @param mixed $hash
     *
     * @return \eZ\Publish\SPI\FieldType\Value
     */
    public function fromHash($hash)
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        // The default constructor at least works for the top level objects.
        // For more complex values a manual conversion is necessary.
        return new Value($hash);
    }

    /**
     * Converts the given $value into a plain hash format.
     *
     * Converts the given $value into a plain hash format, which can be used to
     * transfer the value through plain text formats, e.g. XML, which do not
     * support complex structures like objects. See the class level doc block
     * for additional information. See the class description for more details
     * on a hash format.
     *
     * @param \eZ\Publish\SPI\FieldType\Value $value
     *
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        // Simplest way to ensure a deep structure is cloned and converted into
        // scalars and has maps.
        return json_decode(json_encode($value), true);
    }
}
