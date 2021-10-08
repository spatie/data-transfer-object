<?php

namespace Spatie\DataTransferObject\Descriptors;

use Illuminate\Support\Collection;
use ReflectionAttribute;
use ReflectionProperty;

final class PropertyDescriptor
{
    private Collection $attributes;

    public function __construct(private ClassDescriptor $class, private ReflectionProperty $property)
    {
        $this->resolveAttributes();
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
}
