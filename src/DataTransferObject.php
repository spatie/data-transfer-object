<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ReflectionClass;
use ReflectionProperty;

abstract class DataTransferObject
{
    /** @var array */
    private $propertyValues = [];

    /** @var \Spatie\DataTransferObject\Property[] */
    private $propertyDefinitions = [];

    /** @var array */
    protected $exceptKeys = [];

    /** @var array */
    protected $onlyKeys = [];

    /**
     * @param array $parameters
     *
     * @return static
     */
    public static function immutable(array $parameters): DataTransferObject
    {
        $dto = new static($parameters);

        foreach ($dto->propertyDefinitions as $propertyDefinition) {
            $propertyDefinition->markImmutable();
        }

        return $dto;
    }

    public function __construct(array $inputParameters)
    {
        $class = new ReflectionClass(static::class);

        $properties = $this->getPublicProperties($class);

        foreach ($properties as $property) {
            $propertyName = $property->getName();

            if (
                ! isset($inputParameters[$propertyName])
                && ! $property->isDefault()
                && ! $property->isNullable()
            ) {
                throw DataTransferObjectError::uninitialized($property);
            }

            $value = $inputParameters[$propertyName] ?? $property->getValue($this);

            unset($this->{$propertyName});
            unset($inputParameters[$propertyName]);

            $this->propertyDefinitions[$propertyName] = $property;
            $property->set($value);
        }

        if (count($inputParameters)) {
            throw DataTransferObjectError::unknownProperties(array_keys($inputParameters), $class->getName());
        }
    }

    public function __get($name)
    {
        if (! array_key_exists($name, $this->propertyDefinitions)) {
            throw DataTransferObjectError::fieldNotFound($name);
        }

        return $this->propertyValues[$name];
    }

    public function __set($name, $value)
    {
        if (! array_key_exists($name, $this->propertyDefinitions)) {
            throw DataTransferObjectError::fieldNotFound($name);
        }

        if (
            $this->propertyDefinitions[$name]->isInitialised()
            && $this->propertyDefinitions[$name]->isImmutable()
        ) {
            throw DataTransferObjectError::immutable($name);
        }

        $this->propertyValues[$name] = $value;
    }

    public function all(): array
    {
        $data = [];

        $class = new ReflectionClass(static::class);

        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $reflectionProperty) {
            $data[$reflectionProperty->getName()] = $reflectionProperty->getValue($this);
        }

        return $data;
    }

    /**
     * @param string ...$keys
     *
     * @return static
     */
    public function only(string ...$keys): DataTransferObject
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
    public function except(string ...$keys): DataTransferObject
    {
        $valueObject = clone $this;

        $valueObject->exceptKeys = array_merge($this->exceptKeys, $keys);

        return $valueObject;
    }

    public function toArray(): array
    {
        if (count($this->onlyKeys)) {
            $array = Arr::only($this->all(), $this->onlyKeys);
        } else {
            $array = Arr::except($this->all(), $this->exceptKeys);
        }

        $array = $this->parseArray($array);

        return $array;
    }

    protected function parseArray(array $array): array
    {
        foreach ($array as $key => $value) {
            if (
                $value instanceof DataTransferObject
                || $value instanceof DataTransferObjectCollection
            ) {
                $array[$key] = $value->toArray();

                continue;
            }

            if (! is_array($value)) {
                continue;
            }

            $array[$key] = $this->parseArray($value);
        }

        return $array;
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return array|\Spatie\DataTransferObject\Property[]
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
