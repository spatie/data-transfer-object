<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 27.03.19
 * Time: 13:37
 */

namespace Spatie\DataTransferObject;


class ValidateableDto extends DataTransferObject
{
    /**
     * Check if property passes the basic conditions
     * @param Property $property
     * @param array $parameters
     */
    protected function validateProperty($property, array $parameters): void
    {
        if (!array_key_exists($property->getName(), $parameters)
            && is_null($property->getDefault())
            && !$property->isNullable()
        ) {
            throw DataTransferObjectError::uninitialized($property);
        }
    }
}
