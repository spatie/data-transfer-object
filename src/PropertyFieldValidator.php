<?php

namespace Spatie\DataTransferObject;

use ReflectionProperty;
use ReflectionType;

class PropertyFieldValidator extends FieldValidator
{
    public function __construct(ReflectionProperty $property)
    {
        $this->hasTypeDeclaration = $property->hasType();
        $this->hasDefaultValue = $property->isDefault();
        $this->isNullable = $this->resolveAllowsNull($property);
        $this->isMixed = $this->resolveIsMixed($property);
        $this->isMixedArray = $this->resolveIsMixedArray($property);
        $this->allowedTypes = $this->resolveAllowedTypes($property);
        $this->allowedArrayTypes = [];
    }

    private function resolveAllowsNull(ReflectionProperty $property): bool
    {
        if (! $property->getType()) {
            return true;
        }

        return $property->getType()->allowsNull();
    }

    private function resolveIsMixed(ReflectionProperty $property): bool
    {
        return $property->hasType() === false;
    }

    private function resolveIsMixedArray(ReflectionProperty $property): bool
    {
        // We cast to array to support future union types in PHP 8
        $types = [$property->getType()];

        foreach ($types as $type) {
            if (in_array($type, ['iterable', 'array'])) {
                return true;
            }
        }

        return false;
    }

    private function resolveAllowedTypes(ReflectionProperty $property): array
    {
        // We cast to array to support future union types in PHP 8
        $types = [$property->getType()];

        return $this->normaliseTypes(...$types);
    }

    private function normaliseTypes(?ReflectionType ...$types): array
    {
        return array_filter(array_map(
            fn(?ReflectionType $type) => self::$typeMapping[$type] ?? $type,
            $types
        ));
    }
}
