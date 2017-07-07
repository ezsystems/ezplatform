<?php

namespace Kore\RatingFieldTypeBundle\Storage\FieldType;

use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\Core\FieldType\Value as CoreValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue;

class Type extends FieldType
{
    public function getFieldTypeIdentifier()
    {
        return 'kore-rating';
    }

    protected function createValueFromInput($inputValue)
    {
        // @DISPACTH to Value?
        if (is_string($inputValue)) {
            $inputValue = new Value(['rating' => $inputValue]);
        }

        return $inputValue;
    }

    protected function checkValueStructure(CoreValue $value)
    {
        // @DISPACTH to Value?
        if (!$value->rating) {
            throw new eZ\Publish\Core\Base\Exceptions\InvalidArgumentType(
               '$value->rating',
               'number',
               $value->rating
           );
        }
    }

    public function getEmptyValue()
    {
        return new Value;
    }

    public function validateValidatorConfiguration($validatorConfiguration = [])
    {
        // @EXTENSION-POINT
        return [];
    }

    public function validate(FieldDefinition $fieldDefinition, SPIValue $fieldValue)
    {
        // @EXTENSION-POINT
        return [];
    }

    public function getName(SPIValue $value)
    {
        // @DISPACTH to Value?
        return (string) $value;
    }

    protected function getSortInfo(CoreValue $value)
    {
        // @DISPACTH to Value?
        return $this->getName($value);
    }

    public function fromHash($hash)
    {
        // @DISPACTH to Value?
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        return new Value($hash);
    }

    public function toHash(SPIValue $value)
    {
        // @DISPACTH to Value?
        return get_object_vars($value);
    }

    /**
     * @param \EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\Value $value
     * @return \eZ\Publish\SPI\Persistence\Content\FieldValue
     */
    public function toPersistenceValue(SPIValue $value): FieldValue
    {
        // @EXTENSION-POINT: Write to external storage
        return new FieldValue(
            array(
                "data" => $this->toHash($value),
                "sortKey" => $this->getSortInfo($value),
            )
        );
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\FieldValue $fieldValue
     * @return \EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\Value
     */
    public function fromPersistenceValue(FieldValue $fieldValue)
    {
        // @EXTENSION-POINT: Read from external storage
        if ($fieldValue->data === null) {
            return $this->getEmptyValue();
        }

        return new Value($fieldValue->data);
    }
}
