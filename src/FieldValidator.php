<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ReflectionProperty;

abstract class FieldValidator
{
    public bool $isNullable;

    public bool $isMixed;

    public bool $isMixedArray;

    public bool $hasDefaultValue;

    public array $allowedTypes;

    public array $allowedArrayTypes;

    public array $allowedArrayKeyTypes;

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
            // We assume value types are invalid by default
            $isValidValue = false;

            foreach ($this->allowedArrayTypes as $type) {
                $isValidValue = $this->assertValidArrayValueTypes($type, $value);

                if ($isValidValue) {
                    break;
                }
            }

            // We assume key types are valid by default, because they can be omitted
            $isValidKey = true;

            foreach ($this->allowedArrayKeyTypes as $keyType) {
                $isValidKey = $this->assertValidArrayKeyTypes($keyType, $value);

                if (! $isValidKey) {
                    break;
                }
            }

            if (! $isValidKey) {
                return false;
            }

            if ($isValidValue && $isValidKey) {
                return true;
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

    private function assertValidArrayValueTypes(string $type, $collection): bool
    {
        foreach ($collection as $value) {
            if (! $this->assertValidType($type, $value)) {
                return false;
            }
        }

        return true;
    }

    private function assertValidArrayKeyTypes(string $keyType, $collection): bool
    {
        foreach ($collection as $key => $value) {
            if (! $this->assertValidType($keyType, $key)) {
                return false;
            }
        }

        return true;
    }
}
