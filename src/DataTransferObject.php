<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ReflectionClass;
use ReflectionProperty;

abstract class DataTransferObject
{
    /** @var bool */
    protected $ignoreMissing = false;

    /** @var array */
    protected $exceptKeys = [];

    /** @var array */
    protected $onlyKeys = [];

    /**
     * @param array $parameters
     *
     * @return \Spatie\DataTransferObject\ImmutableDataTransferObject|static
     */
    public static function immutable(array $parameters = []): ImmutableDataTransferObject
    {
        return new ImmutableDataTransferObject(new static($parameters));
    }

    /**
     * @param array $arrayOfParameters
     *
     * @return \Spatie\DataTransferObject\ImmutableDataTransferObject[]|static[]
     */
    public static function arrayOf(array $arrayOfParameters): array
    {
        return array_map(
            function ($parameters) {
                return new static($parameters);
            },
            $arrayOfParameters
        );
    }

    public function __construct(array $parameters = [])
    {
        $validators = $this->getFieldValidators();

        $valueCaster = new ValueCaster();

        foreach ($validators as $field => $validator) {
            if (
                ! isset($parameters[$field])
                && ! $validator->hasDefaultValue
                && ! $validator->isNullable
            ) {
                throw DataTransferObjectError::uninitialized(
                    static::class,
                    $field
                );
            }

            $value = $parameters[$field] ?? $this->{$field} ?? null;

            if (is_array($value)) {
                $value = $valueCaster->cast($value, $validator);
            }

            if (! $validator->isValidType($value)) {
                throw DataTransferObjectError::invalidType(
                    static::class,
                    $field,
                    $validator->allowedTypes,
                    $value
                );
            }

            $this->{$field} = $value;

            unset($parameters[$field]);
        }

        if (! $this->ignoreMissing && count($parameters)) {
            throw DataTransferObjectError::unknownProperties(array_keys($parameters), static::class);
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
     * @return \Spatie\DataTransferObject\FieldValidator[]
     */
    private function getFieldValidators(): array
    {
        return DTOCache::resolve(static::class, function () {
            $class = new ReflectionClass(static::class);

            $properties = [];

            foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
                $field = $reflectionProperty->getName();

                $properties[$field] = FieldValidator::fromReflection($reflectionProperty);
            }

            return $properties;
        });
    }
}
