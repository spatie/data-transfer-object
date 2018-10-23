<?php

namespace Spatie\ValueObject;

use TypeError;

class ValueObjectError extends TypeError
{
    public static function unknownPublicProperty(string $name, string $className): ValueObjectError
    {
        return new self("Public property {$name} not found on {$className}");
    }

    public static function invalidType(Property $property, $value): ValueObjectError
    {
        if ($value === null) {
            $value = 'null';
        }

        if (is_object($value)) {
            $value = get_class($value);
        }

        $expectedTypes = implode(', ', $property->getTypes());

        return new self("Invalid type: expected {$property->getFqn()} to be of type {$expectedTypes}, instead got value `{$value}`.");
    }

    public static function uninitializedProperty(Property $property): ValueObjectError
    {
        return new self("Non-nullable property {$property->getFqn()} has not been initialized.");
    }
}
