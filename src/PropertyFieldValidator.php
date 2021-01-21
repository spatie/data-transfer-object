<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ReflectionNamedType;
use ReflectionProperty;

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
        $this->allowedArrayTypes = $this->resolveAllowedArrayTypes($property);
        $this->allowedArrayKeyTypes = [];
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
        $reflectionType = $property->getType();

        if (! $reflectionType instanceof ReflectionNamedType) {
            return false;
        }

        // We cast to array to support future union types in PHP 8
        $types = [$reflectionType];

        foreach ($types as $type) {
            if (in_array($type->getName(), ['iterable', 'array'])) {
                return true;
            }
        }

        return false;
    }

    private function resolveAllowedTypes(ReflectionProperty $property): array
    {
        // We cast to array to support future union types in PHP 8
        $types = [$property->getType()
            ? $property->getType()->getName()
            : null, ];

        return $this->normaliseTypes(...$types);
    }

    private function resolveAllowedArrayTypes(ReflectionProperty $property): array
    {
        // We cast to array to support future union types in PHP 8
        $types = $property->getType()
            ? $this->resolveAllowedArrayTypesFromCollection($property->getType()->getName())
            : [];

        return $this->normaliseTypes(...$types);
    }
}
