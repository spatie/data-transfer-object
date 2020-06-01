<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ReflectionClass;
use ReflectionProperty;

abstract class FieldValidator
{
    public bool $isNullable;

    public bool $isMixed;

    public bool $isMixedArray;

    public bool $hasDefaultValue;

    public array $allowedTypes;

    public array $allowedArrayTypes;

    protected static array $typeMapping = [
        'int' => 'integer',
        'bool' => 'boolean',
        'float' => 'double',
    ];

    protected bool $hasTypeDeclaration;

    public static function fromReflection(ReflectionProperty $property): FieldValidator
    {
        $docDefinition = null;

        if ($property->getDocComment()) {
            preg_match(
                DocblockFieldValidator::DOCBLOCK_REGEX,
                $property->getDocComment(),
                $matches
            );

            $docDefinition = $matches[0] ?? null;
        }

        if ($docDefinition) {
            return new DocblockFieldValidator($docDefinition, $property->isDefault());
        }

        return new PropertyFieldValidator($property);
    }

    public function isValidType($value): bool
    {
        if (! $this->hasTypeDeclaration) {
            return true;
        }

        if ($this->isMixed) {
            return true;
        }

        if (is_iterable($value) && $this->isMixedArray) {
            return true;
        }

        if ($this->isNullable && $value === null) {
            return true;
        }

        if (is_iterable($value)) {
            foreach ($this->allowedArrayTypes as $type) {
                $isValid = $this->assertValidArrayTypes($type, $value);

                if ($isValid) {
                    return true;
                }
            }
        }

        foreach ($this->allowedTypes as $type) {
            $isValidType = $this->assertValidType($type, $value);

            if ($isValidType) {
                return true;
            }
        }

        return false;
    }

    private function assertValidType(string $type, $value): bool
    {
        return $value instanceof $type || gettype($value) === $type;
    }

    private function assertValidArrayTypes(string $type, $collection): bool
    {
        foreach ($collection as $value) {
            if (! $this->assertValidType($type, $value)) {
                return false;
            }
        }

        return true;
    }

    protected function resolveAllowedArrayTypesFromCollection(string $type): array
    {
        if (!$type || ! class_exists($type) || ! is_subclass_of($type, DataTransferObjectCollection::class)) {
            return [];
        }

        $class = new ReflectionClass($type);

        $currentReturnType = $class->getMethod('current')->getReturnType();

        // We cast to array to support future union types in PHP 8
        $currentReturnTypes = [];
        if ($currentReturnType) {
            $currentReturnTypes[] = $currentReturnType->getName();
        }

        $docblockReturnTypes = $class->getDocComment() ? $this->getCurrentReturnTypesFromDocblock($class->getDocComment()) : [];

        $types = [...$currentReturnTypes, ...$docblockReturnTypes];

        if (! $types) {
            throw DataTransferObjectError::untypedCollection($type);
        }

        return $this->normaliseTypes(...$types);
    }

    /**
     * @return string[]
     */
    private function getCurrentReturnTypesFromDocblock(string $definition): array
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

        return explode('|', $type);
    }

    protected function normaliseTypes(?string ...$types): array
    {
        return array_filter(array_map(
            fn (?string $type) => self::$typeMapping[$type] ?? $type,
            $types
        ));
    }
}
