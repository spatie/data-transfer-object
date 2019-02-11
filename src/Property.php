<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ReflectionProperty;

class Property extends ReflectionProperty
{
    /** @var array */
    protected static $typeMapping = [
        'int' => 'integer',
        'bool' => 'boolean',
        'float' => 'double',
    ];

    /** @var \Spatie\DataTransferObject\DataTransferObject */
    protected $valueObject;

    /** @var bool */
    protected $hasTypeDeclaration = false;

    /** @var bool */
    protected $isNullable = false;

    /** @var bool */
    protected $isInitialised = false;

    /** @var array */
    protected $types = [];

    /** @var array */
    protected $arrayTypes = [];

    public static function fromReflection(DataTransferObject $valueObject, ReflectionProperty $reflectionProperty)
    {
        return new self($valueObject, $reflectionProperty);
    }

    public function __construct(DataTransferObject $valueObject, ReflectionProperty $reflectionProperty)
    {
        parent::__construct($reflectionProperty->class, $reflectionProperty->getName());

        $this->valueObject = $valueObject;

        $this->resolveTypeDefinition();
    }

    public function set($value)
    {
        if (is_array($value)) {
            $value = $this->shouldBeCastToCollection($value) ? $this->castCollection($value) : $this->cast($value);
        }

        if (! $this->isValidType($value)) {
            throw DataTransferObjectError::invalidType($this, $value);
        }

        $this->isInitialised = true;

        $this->valueObject->{$this->getName()} = $value;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function getFqn(): string
    {
        return "{$this->getDeclaringClass()->getName()}::{$this->getName()}";
    }

    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    protected function resolveTypeDefinition()
    {
        $docComment = $this->getDocComment();

        if (! $docComment) {
            $this->isNullable = true;

            return;
        }

        preg_match('/\@var ((?:(?:[\w|\\\\])+(?:\[\])?)+)/', $docComment, $matches);

        if (! count($matches)) {
            $this->isNullable = true;

            return;
        }

        $this->hasTypeDeclaration = true;

        $varDocComment = end($matches);

        $this->types = explode('|', $varDocComment);
        $this->arrayTypes = str_replace('[]', '', $this->types);

        $this->isNullable = strpos($varDocComment, 'null') !== false;
    }

    protected function isValidType($value): bool
    {
        if (! $this->hasTypeDeclaration) {
            return true;
        }

        if ($this->isNullable && $value === null) {
            return true;
        }

        foreach ($this->types as $currentType) {
            $isValidType = $this->assertTypeEquals($currentType, $value);

            if ($isValidType) {
                return true;
            }
        }

        return false;
    }

    protected function cast($value)
    {
        $castTo = null;

        foreach ($this->types as $type) {
            if (! is_subclass_of($type, DataTransferObject::class)) {
                continue;
            }

            $castTo = $type;

            break;
        }

        if (! $castTo) {
            return $value;
        }

        return new $castTo($value);
    }

    protected function castCollection(array $values)
    {
        $castTo = null;

        foreach ($this->arrayTypes as $type) {
            if (! is_subclass_of($type, DataTransferObject::class)) {
                continue;
            }

            $castTo = $type;

            break;
        }

        if (! $castTo) {
            return $values;
        }

        $casts = [];

        foreach ($values as $value) {
            $casts[] = new $castTo($value);
        }

        return $casts;
    }

    protected function shouldBeCastToCollection(array $values): bool
    {
        if (empty($values)) {
            return false;
        }

        foreach ($values as $key => $value) {
            if (is_string($key)) {
                return false;
            }

            if (! is_array($value)) {
                return false;
            }
        }

        return true;
    }

    protected function assertTypeEquals(string $type, $value): bool
    {
        if (strpos($type, '[]') !== false) {
            return $this->isValidGenericCollection($type, $value);
        }

        if ($type === 'mixed' && $value !== null) {
            return true;
        }

        return $value instanceof $type
            || gettype($value) === (self::$typeMapping[$type] ?? $type);
    }

    protected function isValidGenericCollection(string $type, $collection): bool
    {
        if (! is_array($collection)) {
            return false;
        }

        $valueType = str_replace('[]', '', $type);

        foreach ($collection as $value) {
            if (! $this->assertTypeEquals($valueType, $value)) {
                return false;
            }
        }

        return true;
    }
}
