<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

abstract class DataTransferObject
{
    /** @var array */
    protected $allValues = [];

    /** @var array */
    protected $exceptKeys = [];

    /** @var array */
    protected $onlyKeys = [];

    public function __construct(array $parameters)
    {
        $class = new DataTransferObjectDefinition($this);

        $properties = $class->getDataTransferObjectProperties();

        foreach ($properties as $property) {
            if (
                ! isset($parameters[$property->getName()])
                && ! $property->isNullable()
            ) {
                throw DataTransferObjectError::uninitialized($property);
            }

            $value = $parameters[$property->getName()] ?? null;

            $property->set($value);

            unset($parameters[$property->getName()]);

            $this->allValues[$property->getName()] = $property->getValue($this);
        }

        if (count($parameters)) {
            throw DataTransferObjectError::unknownProperties(array_keys($parameters), $class->getName());
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
    public function only(string ...$keys): DataTransferObject
    {
        $dataTransferObject = clone $this;

        $dataTransferObject->onlyKeys = array_merge($this->onlyKeys, $keys);

        return $dataTransferObject;
    }

    /**
     * @param string ...$keys
     *
     * @return static
     */
    public function except(string ...$keys): DataTransferObject
    {
        $dataTransferObject = clone $this;

        $dataTransferObject->exceptKeys = array_merge($this->exceptKeys, $keys);

        return $dataTransferObject;
    }

    public function toArray(): array
    {
        if (count($this->onlyKeys)) {
            return Arr::only($this->all(), $this->onlyKeys);
        }

        return Arr::except($this->all(), $this->exceptKeys);
    }
}
