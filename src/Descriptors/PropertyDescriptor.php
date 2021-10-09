<?php

namespace Spatie\DataTransferObject\Descriptors;

use Illuminate\Support\Collection;
use ReflectionAttribute;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

final class PropertyDescriptor
{
    private Collection $attributes;

    private Collection $types;

    public function __construct(private ClassDescriptor $class, private ReflectionProperty $property)
    {
        $this->resolveAttributes();
        $this->resolveTypes();
    }

    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    /**
     * @template InputAttributeType
     *
     * @param class-string<InputAttributeType> $attribute
     *
     * @return null|InputAttributeType
     */
    public function getAttribute(string $attribute): ?Object
    {
        return $this->attributes->whereInstanceOf($attribute)->first();
    }

    public function getName(): string
    {
        return $this->property->getName();
    }

    public function getType(string $name): ?ReflectionNamedType
    {
        return $this->types->filter(
            fn ($type) => $type->getName() === $name || is_subclass_of($type->getName(), $name)
        )->first();
    }

    public function getTypes(): Collection
    {
        return $this->types;
    }

    public function getTypeNames(): Collection
    {
        return $this->types->map(
            fn ($type) => $type->getName()
        );
    }

    public function hasType(string $name): bool
    {
        return $this->getType($name) !== null;
    }

    public function getValue(): mixed
    {
        return $this->property->getValue($this->class->getDataTransferObject());
    }

    public function setValue(mixed $value): self
    {
        $this->property->setValue($this->class->getDataTransferObject(), $value);

        return $this;
    }

    private function resolveAttributes(): void
    {
        $this->attributes = Collection::make($this->property->getAttributes())
            ->map(
                fn (ReflectionAttribute $attribute) => $attribute->newInstance()
            );
    }

    private function resolveTypes(): void
    {
        $type = $this->property->getType();

        // We do not need to check for null, as readonly properties must have a type.
        if ($type instanceof ReflectionUnionType) {
            $this->types = Collection::make($type->getTypes());
        } else {
            $this->types = Collection::make([ $type ]);
        }
    }
}
