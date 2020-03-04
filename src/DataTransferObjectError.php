<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use TypeError;

class DataTransferObjectError extends TypeError
{
    use HasErrorProperties;

    public function __construct($message, $properties = [])
    {
        parent::__construct($message);
        $this->setProperties($properties);
    }

    public static function unknownProperties(array $properties, string $className): DataTransferObjectError
    {
        $propertyNames = implode('`, `', $properties);

        return new self("Public properties `{$propertyNames}` not found on {$className}", [
            'error'      => __FUNCTION__,
            'class'      => $className,
            'properties' => $properties,
        ]);
    }

    public static function invalidType(
        string $class,
        string $field,
        array $expectedTypes,
        $value
    ): DataTransferObjectError {
        $currentType = gettype($value);

        if ($value === null) {
            $value = 'null';
        }

        if (is_object($value)) {
            $value = get_class($value);
        }

        if (is_array($value)) {
            $value = 'array';
        }

        $expectedTypesList = implode(', ', $expectedTypes);

        return new self("Invalid type: expected `{$class}::{$field}` to be of type `{$expectedTypesList}`, instead got value `{$value}`, which is {$currentType}.", [
            'error'         => __FUNCTION__,
            'class'         => $class,
            'property'      => $field,
            'value'         => $value,
            'type'          => $currentType,
            'expectedTypes' => $expectedTypes,
        ]);
    }

    public static function uninitialized(string $class, string $field): DataTransferObjectError
    {
        return new self("Non-nullable property `{$class}::{$field}` has not been initialized.", [
            'error'    => __FUNCTION__,
            'class'    => $class,
            'property' => $field,
        ]);
    }

    public static function immutable(string $property): DataTransferObjectError
    {
        return new self("Cannot change the value of property {$property} on an immutable data transfer object", [
            'error'    => __FUNCTION__,
            'property' => $property,
        ]);
    }
}
