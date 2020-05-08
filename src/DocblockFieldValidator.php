<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

class DocblockFieldValidator extends FieldValidator
{
    public const DOCBLOCK_REGEX = '/@var ((?:(?:[\w?|\\\\<>])+(?:\[])?)+)/';

    public function __construct(string $definition, bool $hasDefaultValue = false)
    {
        preg_match(
            DocblockFieldValidator::DOCBLOCK_REGEX,
            $definition,
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

                return null;
            },
            explode('|', $definition)
        ));
    }

    private function normaliseTypes(?string ...$types): array
    {
        return array_filter(array_map(
            fn (?string $type) => self::$typeMapping[$type] ?? $type,
            $types
        ));
    }
}
