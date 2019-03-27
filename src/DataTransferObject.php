<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ReflectionClass;
use ReflectionProperty;

/**
 * Class DataTransferObject.
 */
abstract class DataTransferObject implements DtoContract
{
    /** @var array */
    protected $exceptKeys = [];

    /** @var array */
    protected $onlyKeys = [];

    /** @var Property[] | array */
    protected $properties = [];

    /** @var bool */
    protected $immutable;

    public static function mutable(array $parameters): self
    {
        return new static($parameters, false);
    }

    public static function immutable(array $parameters): self
    {
        return new static($parameters, true);
    }

    public function __construct(array $parameters, bool $immutable = true)
    {
        $this->immutable = $immutable;
        $this->boot($parameters);
    }

    /**
     * Boot the dto and process all parameters.
     * @param array $parameters
     * @throws \ReflectionException | DataTransferObjectError
     */
    protected function boot(array $parameters): void
    {
        foreach ($this->getPublicProperties() as $property) {

            /*
             * Do not change the order of the following methods.
             * External packages rely on this order.
             */

            $this->setPropertyDefaultValue($property);

            $property = $this->mutateProperty($property);

            $this->validateProperty($property, $parameters);

            $this->setPropertyValue($property, $parameters);

            /* add the property to an associative array with the name as key */
            $this->properties[$property->getName()] = $property;

            /* remove the property from the parameters array  */
            unset($parameters[$property->getName()]);

            /* remove the property from the value object  */
            unset($this->{$property->getName()});
        }

        $this->processRemainingProperties($parameters);
    }

    /**
     * Get all public properties from the current object through reflection.
     * @return Property[]
     * @throws \ReflectionException
     */
    protected function getPublicProperties(): array
    {
        $class = new ReflectionClass(static::class);

        $properties = [];
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $properties[$reflectionProperty->getName()] = Property::fromReflection($reflectionProperty);
        }

        return $properties;
    }

    /**
     * Check if property passes the basic conditions.
     * @param Property $property
     * @param array $parameters
     */
    protected function validateProperty($property, array $parameters): void
    {
        if (!array_key_exists($property->getName(), $parameters)
            && is_null($property->getDefault())
            && !$property->isNullable()
        ) {
            throw DataTransferObjectError::uninitialized($property);
        }
    }

    /**
     * Set the value if it's present in the array.
     * @param Property $property
     * @param array $parameters
     */
    protected function setPropertyValue($property, array $parameters): void
    {
        if (array_key_exists($property->getName(), $parameters)) {
            $property->set($parameters[$property->getName()]);
        }
    }

    /**
     * Set the value if it's present in the array.
     * @param Property $property
     */
    protected function setPropertyDefaultValue($property): void
    {
        $property->setDefault($property->getValueFromReflection($this));
    }

    /**
     * Allows to mutate the property before it gets processed.
     * @param Property $property
     * @return Property
     */
    protected function mutateProperty($property)
    {
        return $property;
    }

    /**
     * Check if there are additional parameters left.
     * Throw error if there are.
     * Additional properties are not allowed in a dto.
     * @param array $parameters
     * @throws DataTransferObjectError
     */
    protected function processRemainingProperties(array $parameters)
    {
        if (count($parameters)) {
            throw DataTransferObjectError::unknownProperties(array_keys($parameters), static::class);
        }
    }

    /**
     * Immutable behavior
     * Throw error if a user tries to set a property.
     * @param $name
     * @param $value
     * @Throws DataTransferObjectError
     */
    public function __set($name, $value)
    {
        if ($this->immutable) {
            throw DataTransferObjectError::immutable($name);
        }
        if (!isset($this->properties[$name])) {
            throw DataTransferObjectError::propertyNotFound($name, get_class($this));
        }

        if($this->properties[$name]->isImmutable()){
            throw DataTransferObjectError::immutableProperty($name);
        }
        $this->$name = $value;
    }

    /**
     * Proxy through to the properties array.
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->properties[$name]->getValue();
    }

    public function all(): array
    {
        $data = [];

        foreach ($this->properties as $property) {
            $data[$property->getName()] = $property->getValue();
        }

        return $data;
    }

    public function only(string ...$keys): DtoContract
    {
        $this->onlyKeys = array_merge($this->onlyKeys, $keys);

        return $this;
    }

    public function except(string ...$keys): DtoContract
    {
        $this->exceptKeys = array_merge($this->exceptKeys, $keys);

        return $this;
    }

    public function toArray(): array
    {
        if (count($this->onlyKeys)) {
            $array = Arr::only($this->all(), $this->onlyKeys);
        } else {
            $array = Arr::except($this->all(), $this->exceptKeys);
        }

        return $this->parseArray($array);
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
}
