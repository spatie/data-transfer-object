<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ReflectionClass;
use ReflectionProperty;

abstract class DataTransferObject
{
    /** @var array */
    protected $exceptKeys = [];

    /** @var array */
    protected $onlyKeys = [];

    /** @var Property[] | array */
    protected $properties = [];

    /**
     * @param array $parameters
     *
     * @return \Spatie\DataTransferObject\ImmutableDataTransferObject|static
     */
    public static function immutable(array $parameters): ImmutableDataTransferObject
    {
        return new ImmutableDataTransferObject(new static($parameters));
    }

    final public function __construct(array $parameters)
    {
        $class = new ReflectionClass(static::class);

        $properties = $this->getPublicProperties($class);

        foreach ($properties as $property) {
            $property->setDefault($property->getValue($this));
            $method = $property->getName();

            if (method_exists($this, $method)) {
                $factory = new Attribute($property);
                $property = $this->$method($factory)->getProperty();
            }
            $default = $property->getDefault();
            if (
                !isset($parameters[$property->getName()])
                && !isset($default)
                && !$property->isNullable()
                && $property->isRequired()
            ) {
                throw DataTransferObjectError::uninitialized($property);
            }

            if (array_key_exists($property->getName(), $parameters)) {
                $property->set($parameters[$property->getName()]);
            } else {
                $property->setUninitialized();
            }

            $this->properties[$property->getName()] = $property;

            unset($parameters[$property->getName()]);
            unset($this->{$property->getName()});
        }


        if (count($parameters)) {
            throw DataTransferObjectError::unknownProperties(array_keys($parameters), $class->getName());
        }
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

    public function __set($name, $value)
    {
        throw DataTransferObjectError::immutable($name);
    }

    public function __get($name)
    {
        return $this->properties[$name]->getActualValue();
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

            if (!is_array($value)) {
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

    protected function propertyIsOptional($property)
    {
        $isOptionalMethod = $property->getName() . "IsOptional";
        return method_exists($this, $isOptionalMethod) && is_bool($this->$isOptionalMethod()) ? $this->$isOptionalMethod() : false;
    }
}
