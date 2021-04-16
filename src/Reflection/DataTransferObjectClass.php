<?php

namespace Spatie\DataTransferObject\Reflection;

use ReflectionClass;
use ReflectionProperty;
use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\ValidationException;

class DataTransferObjectClass
{
    private ReflectionClass $reflectionClass;
    
    private DataTransferObject $dataTransferObject;
    
    private bool $isStrict;

    private static array $classCache = [];

    private static array $propertyCache = [];

    public function __construct(DataTransferObject $dataTransferObject)
    {
        if (! isset(static::$classCache[$dataTransferObject::class])) {
            static::$classCache[$dataTransferObject::class] = new ReflectionClass($dataTransferObject);
        }
        
        $this->reflectionClass = static::$classCache[$dataTransferObject::class];
        $this->dataTransferObject = $dataTransferObject;
    }

    /**
     * @return \Spatie\DataTransferObject\Reflection\DataTransferObjectProperty[]
     */
    public function getProperties(): array
    {
        if (! isset(static::$propertyCache[$this->dataTransferObject::class])) {
            static::$propertyCache[$this->dataTransferObject::class] = array_filter(
                $this->reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC),
                fn (ReflectionProperty $property) => ! $property->isStatic()
            );
        }

        return array_map(
            fn (ReflectionProperty $property) => new DataTransferObjectProperty(
                $this->dataTransferObject,
                $property
            ),
            static::$propertyCache[$this->dataTransferObject::class]
        );
    }

    public function validate(): void
    {
        $validationErrors = [];

        foreach ($this->getProperties() as $property) {
            $validators = $property->getValidators();

            foreach ($validators as $validator) {
                $result = $validator->validate($property->getValue());

                if ($result->isValid) {
                    continue;
                }

                $validationErrors[$property->name][] = $result;
            }
        }

        if (count($validationErrors)) {
            throw new ValidationException($this->dataTransferObject, $validationErrors);
        }
    }

    public function isStrict(): bool
    {
        return $this->isStrict ??= ! empty($this->reflectionClass->getAttributes(Strict::class));
    }
}
