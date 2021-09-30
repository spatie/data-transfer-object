<?php

namespace Spatie\DataTransferObject;

use ReflectionClass;
use ReflectionProperty;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Attributes\MapTo;
use Spatie\DataTransferObject\Casters\DataTransferObjectCaster;
use Spatie\DataTransferObject\Descriptors\ClassDescriptor;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Spatie\DataTransferObject\Reflection\DataTransferObjectClass;
use Spatie\DataTransferObject\Resolvers\CastResolver;
use Spatie\DataTransferObject\Resolvers\MapFromResolver;

#[CastWith(DataTransferObjectCaster::class)]
abstract class DataTransferObject
{
    private static ClassDescriptor $description;

    protected array $exceptKeys = [];

    protected array $onlyKeys = [];

    public static function new(...$args): static
    {
        $dataTransferObject = new static();

        $class = new DataTransferObjectClass($dataTransferObject);

        return $dataTransferObject
            ->setUp($class, ...$args)
            ->validate($class);
    }

    public static function newWithoutValidation(...$args): static
    {
        $dataTransferObject = new static();

        if (is_array($args[0] ?? null)) {
            $args = $args[0];
        }

        $properties = (new MapFromResolver(
            static::describe()
        ))->mapArguments($args);

        $properties = (new CastResolver(
            static::describe()
        ))->castArguments($properties);

        return new static($properties);
    }

    private static function describe(): ClassDescriptor
    {
        if (! isset(static::$description)) {
            static::$description = new ClassDescriptor(static::class);
        }

        return static::$description;
    }

    private function __construct(array $properties = [])
    {
        foreach ($properties as $property => $value) {
            $this->{$property} = $value;
        }
    }

    protected function setUp(DataTransferObjectClass $class, ...$args): static
    {
        if (is_array($args[0] ?? null)) {
            $args = $args[0];
        }

        foreach ($class->getProperties() as $property) {
            $property->setValue(Arr::get($args, $property->name) ?? $this->{$property->name} ?? null);

            $args = Arr::forget($args, $property->name);
        }

        if ($class->isStrict() && count($args)) {
            throw UnknownProperties::new(static::class, array_keys($args));
        }

        return $this;
    }

    protected function validate(DataTransferObjectClass $class): static
    {
        $class->validate();

        return $this;
    }

    public static function arrayOf(array $arrayOfParameters): array
    {
        return array_map(
            fn (mixed $parameters) => static::new($parameters),
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

            $mapToAttribute = $property->getAttributes(MapTo::class);
            $name = count($mapToAttribute) ? $mapToAttribute[0]->newInstance()->name : $property->getName();

            $data[$name] = $property->getValue($this);
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

    public function clone(...$args): static
    {
        return static::new(...array_merge($this->toArray(), $args));
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

            if (! is_array($value)) {
                continue;
            }

            $array[$key] = $this->parseArray($value);
        }

        return $array;
    }
}
