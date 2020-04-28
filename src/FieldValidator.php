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

            $docDefinition = $matches[1] ?? '';
        }

        if ($docDefinition !== null) {
            return new DocblockFieldValidator($docDefinition, $property->isDefault());
        }

        return new PropertyFieldValidator($property);
    }

    public function __construct(ReflectionProperty $property)
    {
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
}
