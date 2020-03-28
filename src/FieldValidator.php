<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ReflectionProperty;

class FieldValidator
{
    /** @var array */
    private static $typeMapping = [
        'int' => 'integer',
        'bool' => 'boolean',
        'float' => 'double',
    ];

    /** @var bool */
    private $hasTypeDeclaration = false;

    /** @var bool */
    public $isNullable = false;

    /** @var bool */
    public $isMixed = false;

    /** @var bool */
    public $isMixedArray = false;

    /** @var bool */
    public $hasDefaultValue = false;

    /** @var array */
    public $allowedTypes = [];

    /** @var array<string|array{0:string,1:string}> */
    public $allowedArrayTypes = [];


    public static function fromReflection(ReflectionProperty $property): FieldValidator
    {
        return new self(
            $property->getDocComment() ?: null,
            $property->isDefault()
        );
    }

    public function __construct(?string $docComment = null, bool $hasDefaultValue = false)
    {
        preg_match(
            '/@var ((?:(?:[\w?|\\\\<>,])+(?:\[])?)+)/',
            $docComment ?? '',
            $matches
        );

        $definition = $matches[1] ?? '';

        $this->hasTypeDeclaration = $definition !== '';
        $this->hasDefaultValue = $hasDefaultValue;
        $this->isNullable = $this->resolveNullable($definition);
        $this->isMixed = $this->resolveIsMixed($definition);
        $this->isMixedArray = $this->resolveIsMixedArray($definition);
        $this->allowedTypes = $this->resolveAllowedTypes($definition);
        $this->allowedArrayTypes = $this->resolveAllowedArrayTypes($definition);
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

    /**
     * @param string|array $type
     * @param iterable $collection
     * @return bool
     */
    private function assertValidArrayTypes($type, $collection): bool
    {
        foreach ($collection as $key => $value) {
            if (is_array($type)) {
                if (! $this->assertValidType($type[0], $key)) {
                    return false;
                }
                if (! $this->assertValidType($type[1], $value)) {
                    return false;
                }

                continue;
            }

            if (! $this->assertValidType($type, $value)) {
                return false;
            }
        }

        return true;
    }

    private function resolveNullable(string $definition): bool
    {
        if (! $definition) {
            return true;
        }

        if (Str::contains($definition, ['mixed', 'null', '?'])) {
            return true;
        }

        return false;
    }

    private function resolveIsMixed(string $definition): bool
    {
        return Str::contains($definition, ['mixed']);
    }

    private function resolveIsMixedArray(string $definition): bool
    {
        $types = $this->normaliseTypes(...explode('|', $definition));

        foreach ($types as $type) {
            if (in_array($type, ['iterable', 'array'])) {
                return true;
            }
        }

        return false;
    }

    private function resolveAllowedTypes(string $definition): array
    {
        return $this->normaliseTypes(...explode('|', $definition));
    }

    private function resolveAllowedArrayTypes(string $definition): array
    {
        return $this->normaliseTypes(...array_map(
            function (string $type) {
                if (! $type) {
                    return;
                }

                if (strpos($type, '[]') !== false) {
                    return str_replace('[]', '', $type);
                }

                if (strpos($type, 'iterable<') !== false) {
                    return str_replace(['iterable<', '>'], ['', ''], $type);
                }

                if (strpos($type, 'array<') !== false) {
                    $arrayTypes = explode(',', str_replace(['array<', '>'], ['', ''], $type));

                    return count($arrayTypes) == 1 ? $arrayTypes[0] : $arrayTypes;
                }

                return null;
            },
            explode('|', $definition)
        ));
    }

    private function normaliseTypes(...$types): array
    {
        return array_filter(array_map(
            function ($type) {
                if (is_array($type)) {
                    return [
                        self::$typeMapping[$type[0]] ?? $type[0],
                        self::$typeMapping[$type[1]] ?? $type[1],
                    ];
                }

                return self::$typeMapping[$type] ?? $type;
            },
            $types
        ));
    }
}
