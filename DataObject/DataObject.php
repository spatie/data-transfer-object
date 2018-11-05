<?php

namespace Spatie\DataObject;

use ReflectionClass;
use ReflectionProperty;

abstract class DataObject
{
    /** @var array */
    protected $allValues = [];

    /** @var array */
    protected $exceptKeys = [];

    /** @var array */
    protected $onlyKeys = [];

    public function __construct(array $parameters)
    {
        $class = new ReflectionClass(static::class);

        $properties = $this->getPublicProperties($class);

        foreach ($properties as $property) {
            if (
                ! isset($parameters[$property->getName()])
                && ! $property->isNullable()
            ) {
                throw DataObjectError::uninitialized($property);
            }

            $value = $parameters[$property->getName()] ?? null;

            $property->set($value);

            unset($parameters[$property->getName()]);

            $this->allValues[$property->getName()] = $property->getValue($this);
        }

        if (count($parameters)) {
            throw DataObjectError::unknownProperties(array_keys($parameters), $class);
        }
    }

    public function all(): array
    {
        return $this->allValues;
    }

    /**
     * @param string ...$keys
     *
     * @return static
     */
    public function only(string ...$keys): DataObject
    {
        $valueObject = clone $this;

        $valueObject->onlyKeys = array_merge($this->onlyKeys, $keys);

        return $valueObject;
    }

    /**
     * @param string ...$keys
     *
     * @return static
     */
    public function except(string ...$keys): DataObject
    {
        $valueObject = clone $this;

        $valueObject->exceptKeys = array_merge($this->exceptKeys, $keys);

        return $valueObject;
    }

    public function toArray(): array
    {
        if (count($this->onlyKeys)) {
            return Arr::only($this->all(), $this->onlyKeys);
        }

        return Arr::except($this->all(), $this->exceptKeys);
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return array|\Spatie\DataObject\Property[]
     */
    protected function getPublicProperties(ReflectionClass $class): array
    {
        $properties = [];

        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $properties[$reflectionProperty->getName()] = Property::fromReflection($this, $reflectionProperty);
        }

        return $properties;
    }
}
