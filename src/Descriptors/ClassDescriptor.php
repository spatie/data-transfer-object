<?php

namespace Spatie\DataTransferObject\Descriptors;

use LogicException;
use ReflectionClass;
use ReflectionProperty;
use Spatie\DataTransferObject\DataTransferObject;

class ClassDescriptor
{
    private string $dataTransferObject;

    private ReflectionClass $reflectionClass;

    private array $properties;

    public function __construct(string $dataTransferObject)
    {
        if (! is_subclass_of($dataTransferObject, DataTransferObject::class)) {
            throw new LogicException(
                ClassDescriptor::class . " can only describe classes that extend " . DataTransferObject::class . "."
            );
        }

        $this->dataTransferObject = $dataTransferObject;
        $this->reflectionClass = new ReflectionClass($dataTransferObject);
    }

    public function getClassFqdn(): string
    {
        return $this->dataTransferObject;
    }

    public function getReflection(): ReflectionClass
    {
        return $this->reflectionClass;
    }

    public function getPropertyByName(string $name): PropertyDescriptor
    {
        $property = array_values(
            array_filter($this->getProperties(), fn ($property) => $property->getName() === $name)
        )[0] ?? null;

        if ($property === null) {
            throw new LogicException(
                "Property with the name [$name] does not exist on " . $this->getClassFqdn() . "."
            );
        }

        return $property;
    }

    /**
     * @return array<PropertyDescriptor>
     */
    public function getProperties(): array
    {
        if (isset($this->properties)) {
            return $this->properties;
        }

        $properties = [];

        foreach ($this->reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $properties[] = new PropertyDescriptor($property);
        }

        return $this->properties = $properties;
    }
}
