<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

trait HasErrorProperties
{
    /** @var string */
    protected $error = '';

    /** @var string */
    protected $class = '';

    /** @var string */
    protected $property = '';

    /** @var array */
    protected $properties = [];

    /** @var mixed */
    protected $value;

    /** @var string */
    protected $type = '';

    /** @var array */
    protected $expectedTypes = [];

    /**
     * Set the error properties
     *
     * @param array $properties
     */
    protected function setProperties(array $properties): void
    {
        foreach ($properties as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Get the type of error being thrown
     * The error type corresponds to the name of the function that generated it
     *
     * Error types: unknownProperties, invalidType, uninitialized, immutable
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Get the name of the data transfer object class
     *
     * Applies to error types: unknownProperties, invalidType, uninitialized, immutable
     *
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Get the name of the data transfer object property
     *
     * Applies to error types: invalidType, uninitialized, immutable
     *
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * Get the names of the data transfer object properties
     *
     * Applies to error types: unknownProperties
     *
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Get the value of the data transfer object property
     *
     * Applies to error types: invalidType
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the type of the data transfer object property
     *
     * Applies to error types: invalidType
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the expected types of the data transfer object property
     *
     * Applies to error types: invalidType
     *
     * @return array
     */
    public function getExpectedTypes(): array
    {
        return $this->expectedTypes;
    }
}
