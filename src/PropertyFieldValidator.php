<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
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
        $this->allowedArrayTypes = $this->resolveAllowedArrayTypes($property);
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
        $types = [$property->getType()];

        return $this->normaliseTypes(...$types);
    }

    private function resolveAllowedArrayTypes(ReflectionProperty $property): array
    {
        $type = $property->getType();

        if (!$type || ! class_exists($type->getName())) {
            return [];
        }

        $class = new ReflectionClass($type->getName());

        if (! $class->isSubclassOf(DataTransferObjectCollection::class)) {
            return [];
        }

        $currentReturnType = $class->getMethod('current')->getReturnType();

        $docblockReturnType = $class->getDocComment() ? $this->getCurrentReturnTypeFromDocblock($class->getDocComment()) : null;

        $types = [$currentReturnType, $docblockReturnType];

        return $this->normaliseTypes(...$types);
    }

    private function getCurrentReturnTypeFromDocblock($definition): ?ReflectionType
    {
        $DOCBLOCK_REGEX = '/@method ((?:(?:[\w?|\\\\<>])+(?:\[])?)+) current/';

        preg_match(
            $DOCBLOCK_REGEX,
            $definition,
            $matches
        );

        $type = $matches[1] ?? null;

        if (! $type) {
            return null;
        }

        return new ReflectionType($type);
    }

    private function normaliseTypes(?ReflectionType ...$types): array
    {
        return array_filter(array_map(
            function (?ReflectionType $type) {
                if ($type instanceof ReflectionNamedType) {
                    $type = $type->getName();
                }

                return self::$typeMapping[$type] ?? $type;
            },
            $types
        ));
    }
}
