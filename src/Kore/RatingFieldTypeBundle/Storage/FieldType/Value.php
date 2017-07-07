<?php

namespace Kore\RatingFieldTypeBundle\Storage\FieldType;

use eZ\Publish\Core\FieldType\Value as BaseValue;

class Value extends BaseValue
{
    public $rating = 3;

    public function __toString(): string
    {
        return (string) $this->rating;
    }
}
