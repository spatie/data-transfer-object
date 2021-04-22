<?php

namespace Spatie\DataTransferObject;

use ReflectionClass;
use ReflectionProperty;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Attributes\UncastWith;
use Spatie\DataTransferObject\Attributes\DefaultUncast;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Spatie\DataTransferObject\Casters\DataTransferObjectCaster;
use Spatie\DataTransferObject\Reflection\DataTransferObjectClass;

#[CastWith(DataTransferObjectCaster::class)]
abstract class DataTransferObject
{
    protected array $exceptKeys = [];

    protected array $onlyKeys = [];

    public function __construct(...$args)
    {
        if (is_array($args[0] ?? null)) {
            $args = $args[0];
        }

        $class = new DataTransferObjectClass($this);

        foreach ($class->getProperties() as $property) {
            $property->setValue($args[$property->name] ?? null);

            unset($args[$property->name]);
        }

        if ($class->isStrict() && count($args)) {
            throw UnknownProperties::new(static::class, array_keys($args));
        }

        $class->validate();
    }

    public static function arrayOf(array $arrayOfParameters): array
    {
        return array_map(
            fn (mixed $parameters) => new static($parameters),
            $arrayOfParameters
        );
    }

    public function all(): array
    {
        $data = [];

        $class = new ReflectionClass(static::class);

        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $data[$property->getName()] = $property->getValue($this);
        }

        return $data;
    }

    public function only(string ...$keys): static
    {
        $dataTransferObject = clone $this;

        $dataTransferObject->onlyKeys = [...$this->onlyKeys, ...$keys];

        return $dataTransferObject;
    }

    public function except(string ...$keys): static
    {
        $dataTransferObject = clone $this;

        $dataTransferObject->exceptKeys = [...$this->exceptKeys, ...$keys];

        return $dataTransferObject;
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
            if ($value instanceof DataTransferObject) {
                $array[$key] = $value->toArray();

                continue;
            }

            if ($uncaster = $this->resolveUncaster($key)) {
                $array[$key] = $uncaster->uncast($value);

                continue;
            }

            if (! is_array($value)) {
                continue;
            }

            $array[$key] = $this->parseArray($value);
        }

        return $array;
    }

    private function resolveUncaster($key): ?Uncaster
    {
        $reflectionClass = new ReflectionClass($this);
        $reflectionProperty = $reflectionClass->getProperty($key);
        $attributes = $reflectionProperty->getAttributes(UncastWith::class);

        if (! count($attributes)) {
            $attributes = $this->resolveUncasterFromType($reflectionProperty);
        }

        if (! count($attributes)) {
            return $this->resolveUncasterFromDefaults($reflectionProperty);
        }

        /** @var \Spatie\DataTransferObject\Attributes\UncastWith $attribute */
        $attribute = $attributes[0]->newInstance();

        return new $attribute->uncasterClass(
            $reflectionProperty->getType()?->getName()
        );
    }

    private function resolveUncasterFromType($reflectionProperty): array
    {
        $type = $reflectionProperty->getType();

        if (! $type) {
            return [];
        }

        if (! class_exists($type->getName())) {
            return [];
        }

        $reflectionClass = new ReflectionClass($type->getName());

        do {
            $attributes = $reflectionClass->getAttributes(UncastWith::class);

            $reflectionClass = $reflectionClass->getParentClass();
        } while (! count($attributes) && $reflectionClass);

        return $attributes;
    }

    private function resolveUncasterFromDefaults($reflectionProperty): ?Uncaster
    {
        $defaultUncastAttributes = $reflectionProperty->getDeclaringClass()->getAttributes(DefaultUncast::class);

        if (! count($defaultUncastAttributes)) {
            return null;
        }

        foreach ($defaultUncastAttributes as $defaultUncastAttribute) {
            /** @var \Spatie\DataTransferObject\Attributes\DefaultUncast $defaultUncast */
            $defaultUncast = $defaultUncastAttribute->newInstance();

            if ($defaultUncast->accepts($reflectionProperty)) {
                return $defaultUncast->resolveUncaster();
            }
        }

        return null;
    }
}
