<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use TypeError;

class DataTransferObjectError extends TypeError
{
    public static function unknownProperties(array $properties, string $className): DataTransferObjectError
    {
        $propertyNames = implode('`, `', $properties);

        return new self("Public properties `{$propertyNames}` not found on {$className}");
    }

    public static function invalidType(Property $property, $value): DataTransferObjectError
    {
        if ($value === null) {
            $value = 'null';
        }

        if (is_object($value)) {
            $value = get_class($value);
        }

        if (is_array($value)) {
            $value = 'array';
        }

        $expectedTypes = implode(', ', $property->getTypes());

        $currentType = gettype($value);

        return new self("Invalid type: expected {$property->getFqn()} to be of type {$expectedTypes}, instead got value `{$value}` ({$currentType}).");
    }

    public static function uninitialized(Property $property): DataTransferObjectError
    {
        return new self("Non-nullable property {$property->getFqn()} has not been initialized.");
    }

    public static function immutable(string $property): DataTransferObjectError
    {
        return new self("Cannot change the value of property {$property} on an immutable data transfer object");
    }
}
