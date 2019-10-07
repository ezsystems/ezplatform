<?php

namespace Kore\RatingFieldTypeBundle\Storage\Legacy;

use Doctrine\DBAL\Connection;
use eZ\Publish\Core\FieldType\Keyword\KeywordStorage\Gateway as BaseGateway;
use eZ\Publish\SPI\Persistence\Content\Field;
use RuntimeException;

class Gateway extends BaseGateway
{
    const RATING_TABLE = 'kore_rating';

    protected $virtualDatabase = [self::RATING_TABLE => []];

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Stores the keyword list from $field->value->externalData.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Field
     * @param int $contentTypeId
     */
    public function storeFieldData(Field $field, $contentTypeId)
    {
        $this->virtualDatabase[self::RATING_TABLE][$field->id] = $field->value->externalData;
    }

    /**
     * Sets the list of assigned keywords into $field->value->externalData.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     */
    public function getFieldData(Field $field)
    {
        $field->value->externalData = ['rating' => 3];

        if (isset($this->virtualDatabase[self::RATING_TABLE][$field->id])) {
            $field->value->externalData = $this->virtualDatabase[self::RATING_TABLE][$field->id];
        }
    }

    /**
     * Retrieve the ContentType ID for the given $field.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     *
     * @return int
     */
    public function getContentTypeId(Field $field)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->select($this->connection->quoteIdentifier('contentclass_id'))
            ->from($this->connection->quoteIdentifier('ezcontentclass_attribute'))
            ->where(
                $query->expr()->eq('id', ':fieldDefinitionId')
            )
            ->setParameter(':fieldDefinitionId', $field->fieldDefinitionId);

        $statement = $query->execute();

        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            throw new RuntimeException(
                sprintf(
                    'Content Type ID cannot be retrieved based on the field definition ID "%s"',
                    $field->fieldDefinitionId
                )
            );
        }

        return intval($row['contentclass_id']);
    }

    /**
     * Deletes keyword data for the given $fieldId.
     *
     * @param int $fieldId
     */
    public function deleteFieldData($fieldId)
    {
        unset($this->virtualDatabase[self::RATING_TABLE][$fieldId]);
    }
}
