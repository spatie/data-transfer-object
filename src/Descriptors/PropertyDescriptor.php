<?php

namespace Spatie\DataTransferObject\Descriptors;

use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

class PropertyDescriptor
{
    public function __construct(private ReflectionProperty $property)
    {
        //
    }

    public function getName(): string
    {
        return $this->property->getName();
    }

    public function getReflection(): ReflectionProperty
    {
        return $this->property;
    }

    /**
     * @return array<ReflectionType>
     */
    public function getTypes(): array
    {
        $type = $this->property->getType();

        if (! $type) {
            return [];
        }

        return match ($type::class) {
            ReflectionNamedType::class => [$type],
            ReflectionUnionType::class => $type->getTypes(),
        };
    }

    public function getTypeNames(): array
    {
        return array_map(
            fn($type) => $type->getName(),
            $this->getTypes()
        );
    }

    public function hasType(string $type): bool
    {
        return in_array($type, $this->getTypeNames());
    }

    public function isOptional(): bool
    {
        if ($this->getTypes()[0]?->allowsNull() || $this->hasType('null')) {
            return true;
        }

        return false;
    }
}
