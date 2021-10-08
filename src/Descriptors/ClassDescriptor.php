<?php

namespace Spatie\DataTransferObject\Descriptors;

use Illuminate\Support\Collection;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

final class ClassDescriptor
{
    private DataTransferObject $dataTransferObject;

    private Collection $arguments;

    private ReflectionClass $reflectionClass;

    private Collection $attributes;

    private Collection $properties;

    public function __construct(DataTransferObject $dataTransferObject, array $arguments = [])
    {
        $this->dataTransferObject = $dataTransferObject;
        $this->arguments = new Collection($arguments);
        $this->reflectionClass = new ReflectionClass($dataTransferObject);

        $this->resolveAttributes();
        $this->resolveProperties();
    }

    public function getArguments(): Collection
    {
        return $this->arguments;
    }

    public function setArguments(Collection $arguments): self
    {
        $this->arguments = $arguments;

        return $this;
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

    public function getDataTransferObject(): DataTransferObject
    {
        return $this->dataTransferObject;
    }

    public function getFqdn(): string
    {
        return $this->reflectionClass->getName();
    }

    public function getProperties(): Collection
    {
        return $this->properties;
    }

    public function getProperty(string $name): ?PropertyDescriptor
    {
        return $this->properties->filter(
            fn($property) => $property->getName() === $name
        )->first();
    }

    public function getReflection(): ReflectionClass
    {
        return $this->reflectionClass;
    }

    public function isStrict(): bool
    {
        return $this->getAttribute(Strict::class) !== null;
    }

    private function resolveAttributes(): void
    {
        $this->attributes = Collection::make($this->reflectionClass->getAttributes())
            ->map(
                fn(ReflectionAttribute $attribute) => $attribute->newInstance()
            );
    }

    private function resolveProperties(): void
    {
        $this->properties = Collection::make(
            $this->reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC)
        )->filter(
            fn (ReflectionProperty $property) => $property->isReadOnly() && $property->isStatic() === false
        )->map(
            fn(ReflectionProperty $property) => new PropertyDescriptor($this, $property)
        );
    }
}
