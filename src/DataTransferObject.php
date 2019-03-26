<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ReflectionClass;
use ReflectionProperty;

/**
 * Class DataTransferObject.
 */
abstract class DataTransferObject
{
    /** @var array */
    protected $exceptKeys = [];

    /** @var array */
    protected $onlyKeys = [];

    /** @var Property[] | array */
    protected $properties = [];

    /** @var bool */
    protected $immutable;

    /**
     * @param array $parameters
     *
     * @return \Spatie\DataTransferObject\ImmutableDataTransferObject|static
     */
    public static function mutable(array $parameters): self
    {
        return new static($parameters, false);
    }

    /**
     * @param array $parameters
     *
     * @return \Spatie\DataTransferObject\ImmutableDataTransferObject|static
     */
    public static function immutable(array $parameters): self
    {
        return new static($parameters, true);
    }

    /**
     * DataTransferObject constructor.
     * @param array $parameters
     */
    final public function __construct(array $parameters, $immutable = true)
    {
        $this->immutable = $immutable;
        $this->boot($parameters);
    }

    /**
     * @param array $parameters
     */
    protected function boot(array $parameters): void
    {
        foreach ($this->getPublicProperties() as $property) {

            /* Setting the default value of the property */
            $property->setDefault($property->getValue($this));

            /* If a attribute method is set on the dto process it */
            if (method_exists($this, $method = $property->getName())) {
                $property = $this->$method(new Attribute($property))->getProperty();
            }

            /* Check if property passes the basic conditions */
            if (! array_key_exists($property->getName(), $parameters)
                && $property->isRequired()
                && is_null($property->getDefault())
                && ! $property->isNullable()
            ) {
                throw DataTransferObjectError::uninitialized($property);
            }

            /* set the value if it's present in the array and mark it as uninitialized otherwise */
            if (array_key_exists($property->getName(), $parameters)) {
                $property->set($parameters[$property->getName()]);
            } else {
                $property->setUninitialized();
            }

            /* add the property to an associative array with the name as key */
            $this->properties[$property->getName()] = $property;

            /* remove the property from the parameters array  */
            unset($parameters[$property->getName()]);
            /* remove the property from the dto  */
            unset($this->{$property->getName()});
        }

        /* Check if there are additional parameters left.
         * Throw error if there are.
         * Additional properties are not allowed in a dto.
         */
        if (count($parameters)) {
            throw DataTransferObjectError::unknownProperties(array_keys($parameters), static::class);
        }

        $this->validate();
    }

    protected function validate()
    {
        //IMPLEMENT VALIDATION FUNCTIONALITY CHECK THE RULES & CONSTRAINTS
    }

    /**
     * @return array
     */
    public function all(): array
    {
        $data = [];

        foreach ($this->properties as $property) {
            $data[$property->getName()] = $property->getActualValue();
        }

        return $data;
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
    }

    /**
     * Proxy through to the properties array.
     * @param $name
     * @return mixed
     */
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

    /**
     * @return array
     */
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

    /**
     * @param array $array
     * @return array
     */
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
    protected function getPublicProperties(): array
    {
        $class = new ReflectionClass(static::class);
        $properties = [];
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $properties[$reflectionProperty->getName()] = Property::fromReflection($this, $reflectionProperty);
        }

        return $properties;
    }
}
