<?php

namespace Kore\RatingFieldTypeBundle\Tests;

use eZ\Publish\SPI\Tests\FieldType\BaseIntegrationTest;
use eZ\Publish\Core\FieldType;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Field;

use Kore\RatingFieldTypeBundle\Storage\FieldType\Type;
use Kore\RatingFieldTypeBundle\Storage\FieldType\Value;
use Kore\RatingFieldTypeBundle\Storage\Legacy\Converter;
use Kore\RatingFieldTypeBundle\Storage\Legacy\Storage;
use Kore\RatingFieldTypeBundle\Storage\Legacy\Gateway;

class TypeIntegrationTest extends BaseIntegrationTest
{
    /**
     * Returns the identifier of the FieldType under test.
     *
     * @return string
     */
    public function getTypeName()
    {
        return 'koreRating';
    }

    /**
     * Returns the Handler with all necessary objects registered.
     *
     * Returns an instance of the Persistence Handler where the
     * FieldType\Storage has been registered.
     *
     * @return \eZ\Publish\SPI\Persistence\Handler
     */
    public function getCustomHandler()
    {
        $fieldType = new Type();

        return $this->getHandler(
            'koreRating',
            $fieldType,
            new Converter(),
            new Storage(
                new Gateway(
                    $this->getDatabaseHandler()->getConnection()
                )
            )
        );
    }

    /**
     * Returns the FieldTypeConstraints to be used to create a field definition
     * of the FieldType under test.
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldTypeConstraints
     */
    public function getTypeConstraints()
    {
        return new Content\FieldTypeConstraints();
    }

    /**
     * Returns the field definition data expected after loading the newly
     * created field definition with the FieldType under test.
     *
     * This is a PHPUnit data provider
     *
     * @return array
     */
    public function getFieldDefinitionData()
    {
        return array(
            // The ezkeyword field type does not have any special field definition
            // properties
            array('fieldType', 'koreRating'),
            array('fieldTypeConstraints', new Content\FieldTypeConstraints()),
        );
    }

    /**
     * Get initial field value.
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldValue
     */
    public function getInitialValue()
    {
        return new Content\FieldValue(
            array(
                'data' => array(),
                'externalData' => array('rating' => 3),
                'sortKey' => '3',
            )
        );
    }

    /**
     * Get update field value.
     *
     * Use to update the field
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldValue
     */
    public function getUpdatedValue()
    {
        return new Content\FieldValue(
            array(
                'data' => array(),
                'externalData' => array('rating' => 5),
                'sortKey' => '5',
            )
        );
    }
}
