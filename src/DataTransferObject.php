<?php
declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ReflectionClass;
use ReflectionProperty;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\DataTransferObjectCaster;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Spatie\DataTransferObject\Reflection\DataTransferObjectClass;
use Spatie\DataTransferObject\Support\Str;

#[CastWith(DataTransferObjectCaster::class)]
abstract class DataTransferObject
{
    protected array $exceptKeys = [];

    protected array $onlyKeys = [];

    const CAMEL = "camelCase";
    const SNAKE = "snake_case";
    const STUDLY = "StudlyCase";

    public function __construct(...$args)
    {
        if (is_array($args[0] ?? null)) {
            $args = $args[0];
        }

        $class = new DataTransferObjectClass($this);

        $isStricType = $class->isStrictType();

        foreach ($class->getProperties() as $property) {
            $property->setValue($args[$property->name] ?? $this->{$property->name} ?? null, $property->name, $isStricType);

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

    public function clone(...$args): static
    {
        return new static(...array_merge($this->toArray(), $args));
    }

    public function toArray(string $type = ""): array
    {
        if (count($this->onlyKeys)) {
            $array = Arr::only($this->all(), $this->onlyKeys);
        } else {
            $array = Arr::except($this->all(), $this->exceptKeys);
        }

        $array = $this->parseArray($array, $type);

        return $array;
    }

    public function toArraySnakeCase(): array
    {
        return $this->toArray(self::SNAKE);
    }

    public function toArrayCamelCase(): array
    {
        return $this->toArray(self::CAMEL);
    }

    public function toArrayStudlyCase(): array
    {
        return $this->toArray(self::STUDLY);
    }

    protected function parseArray(array $array, string $type = ""): array
    {
        $arrayTransformed = [];

        foreach ($array as $key => $value) {
            switch ($type) {
                case self::CAMEL:
                    $key = Str::camel($key);
                    break;
                case self::SNAKE:
                    $key = Str::snake($key);
                    break;
                case self::STUDLY:
                    $key = Str::studly($key);
                    break;
            }

            if ($value instanceof DataTransferObject) {
                $arrayTransformed[$key] = $value->toArray($type);

                continue;
            }

            if (!is_array($value)) {
                $arrayTransformed[$key] = $value;

                continue;
            }

            $arrayTransformed[$key] = $this->parseArray($value, $type);
        }

        return $arrayTransformed;
    }
}
