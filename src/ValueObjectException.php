<?php

namespace Spatie\ValueObject;

use Exception;

class ValueObjectException extends Exception
{
    public static function unknownPublicProperty(string $name, string $className): ValueObjectException
    {
        return new self("Public property {$name} not found on {$className}");
    }

    public static function invalidType(
        string $propertyName,
        string $className,
        string $expectedType,
        $value
    ): ValueObjectException {
        if ($value === null) {
            $value = 'null';
        }

        if (is_object($value)) {
            $value = get_class($value);
        }

        return new self("Invalid type: expected {$className}::{$propertyName} to be of type {$expectedType}; instead got value `{$value}`.");
    }

    public static function uninitialisedProperty(string $name): ValueObjectException
    {
        return new self("Non-nullable property `{$name}` has not been initialised.");
    }
}
