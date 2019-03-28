<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ReflectionClass;
use ReflectionProperty;
use Spatie\DataTransferObject\Contracts\immutable;
use Spatie\DataTransferObject\Contracts\DtoContract;
use Spatie\DataTransferObject\Contracts\PropertyContract;
use Spatie\DataTransferObject\Exceptions\ImmutableDtoException;
use Spatie\DataTransferObject\Exceptions\PropertyNotFoundDtoException;
use Spatie\DataTransferObject\Exceptions\ImmutablePropertyDtoException;
use Spatie\DataTransferObject\Exceptions\UnknownPropertiesDtoException;
use Spatie\DataTransferObject\Exceptions\UninitialisedPropertyDtoException;

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
    protected $immutable = false;

    public function __construct(array $parameters)
    {
        $this->boot($parameters);
    }

    /**
     * Boot the dto and process all parameters.
     * @param array $parameters
     * @throws \ReflectionException | DataTransferObjectError
     */
    protected function boot(array $parameters): void
    {
        $this->resolveImmutable();

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

            /* remove the property from the value object and parameters array  */
            unset($parameters[$property->getName()], $this->{$property->getName()});
        }

        $this->processRemainingProperties($parameters);
    }

    protected function resolveImmutable()
    {
        if ($this instanceof immutable) {
            $this->immutable = true;
        }
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
     * @param PropertyContract $property
     * @param array $parameters
     */
    protected function validateProperty(PropertyContract $property, array $parameters): void
    {
        if (! array_key_exists($property->getName(), $parameters)
            && is_null($property->getDefault())
            && ! $property->nullable()
        ) {
            throw new UninitialisedPropertyDtoException($property);
        }
    }

    /**
     * Set the value if it's present in the array.
     * @param PropertyContract $property
     * @param array $parameters
     */
    protected function setPropertyValue(PropertyContract $property, array $parameters): void
    {
        if (array_key_exists($property->getName(), $parameters)) {
            $property->set($parameters[$property->getName()]);
        }
    }

    /**
     * Set the value if it's present in the array.
     * @param PropertyContract $property
     */
    protected function setPropertyDefaultValue(PropertyContract $property): void
    {
        $property->setDefault($property->getValueFromReflection($this));
    }

    /**
     * Allows to mutate the property before it gets processed.
     * @param PropertyContract $property
     * @return PropertyContract
     */
    protected function mutateProperty(PropertyContract $property): PropertyContract
    {
        return $property;
    }

    /**
     * Check if there are additional parameters left.
     * Throw error if there are.
     * Additional properties are not allowed in a dto.
     * @param array $parameters
     * @throws UnknownPropertiesDtoException
     */
    protected function processRemainingProperties(array $parameters)
    {
        if (count($parameters)) {
            throw new UnknownPropertiesDtoException(array_keys($parameters), static::class);
        }
    }

    /**
     * Immutable behavior
     * Throw error if a user tries to set a property.
     * @param $name
     * @param $value
     * @throws ImmutableDtoException|ImmutablePropertyDtoException|PropertyNotFoundDtoException
     */
    public function __set($name, $value)
    {
        if ($this->immutable) {
            throw new ImmutableDtoException($name);
        }
        if (! isset($this->properties[$name])) {
            throw new PropertyNotFoundDtoException($name, get_class($this));
        }

        if ($this->properties[$name]->immutable()) {
            throw new ImmutablePropertyDtoException($name);
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

    /**
     * @return static
     */
    public function mutable(): DtoContract
    {
        $this->immutable = false;

        return $this;
    }

    /**
     * @return static
     */
    public function immutable(): DtoContract
    {
        $this->immutable = true;

        return $this;
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
        foreach ($keys as $key) {
            $property = $this->properties[$key] ?? null;
            if (isset($property)) {
                $property->setVisible(false);
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        $data = $this->all();
        $array = [];

        if (count($this->onlyKeys)) {
            $array = array_intersect_key($data, array_flip((array) $this->onlyKeys));
        } else {
            foreach ($data as $key => $propertyValue) {
                if ($this->properties[$key]->isVisible()) {
                    $array[$key] = $propertyValue;
                }
            }
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

            if (! is_array($value)) {
                continue;
            }

            $array[$key] = $this->parseArray($value);
        }

        return $array;
    }
}
