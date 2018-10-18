<?php

namespace Spatie\ValueObject;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

abstract class ValueObject
{
    private static $typeMapping = [
        'int' => 'integer',
        'bool' => 'boolean',
    ];

    /** @var array */
    protected $exceptKeys = [];

    /** @var array */
    protected $onlyKeys = [];

    /** @var array */
    protected $fillable = [];

    public function __construct(array $parameters)
    {
        $class = new ReflectionClass(static::class);

        foreach ($parameters as $propertyName => $value) {
            try {
                $property = $class->getProperty($propertyName);
            } catch (ReflectionException $exception) {
                $property = null;
            }

            if (! $property || ! $property->isPublic()) {
                throw ValueObjectException::unknownPublicProperty($propertyName, $class->getName());
            }

            $this->validateType($value, $property);

            $this->{$propertyName} = $value;
        }
    }

    public function all(): array
    {
        $class = new ReflectionClass(static::class);

        $values = [];

        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $values[$property->getName()] = $property->getValue($this);
        }

        return $values;
    }

    /**
     * @param string ...$keys
     *
     * @return static
     */
    public function only(string ...$keys)
    {
        $this->onlyKeys = array_merge($this->onlyKeys, $keys);

        return $this;
    }

    /**
     * @param string ...$keys
     *
     * @return static
     */
    public function except(string ...$keys)
    {
        $this->exceptKeys = array_merge($this->exceptKeys, $keys);

        return $this;
    }

    /**
     * @return static
     */
    public function fillable()
    {
        $this->only(...$this->fillable);

        return $this;
    }

    public function toArray(): array
    {
        if (count($this->onlyKeys)) {
            return Arr::only($this->all(), $this->onlyKeys);
        }

        return Arr::except($this->all(), $this->exceptKeys);
    }

    private function validateType($value, ReflectionProperty $property)
    {
        $docComment = $property->getDocComment();

        if (! $docComment) {
            return true;
        }

        preg_match('/\@var ([\w|]+)/', $docComment, $matches);

        if (! count($matches)) {
            return true;
        }

        $varDocComment = end($matches);

        $types = explode('|', $varDocComment);

        $isValidType = false;

        $isNullable = strpos($varDocComment, 'null') !== false;

        foreach ($types as $type) {
            $isValidType = $this->assertValidType($type, $value, $isNullable);

            if ($isValidType) {
                break;
            }
        }

        if (! $isValidType) {
            $expectedTypes = implode(', ', $types);

            throw ValueObjectException::invalidType(
                $property->getName(),
                $property->getDeclaringClass()->getName(),
                $expectedTypes,
                $value
            );
        }
    }

    private function assertValidType(string $type, $value, bool $isNullable): bool
    {
        if ($isNullable && $value === null) {
            return true;
        }

        if (class_exists($type)) {
            return $value instanceof $type;
        }

        return gettype($value) === (self::$typeMapping[$type] ?? $type);
    }

}
