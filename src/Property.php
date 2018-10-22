<?php

namespace Spatie\ValueObject;

use ReflectionProperty;

class Property extends ReflectionProperty
{
    private static $typeMapping = [
        'int' => 'integer',
        'bool' => 'boolean',
    ];

    /** @var \Spatie\ValueObject\ValueObject */
    protected $valueObject;

    private $hasTypeDeclaration = false;

    private $isNullable = false;

    private $isInitialised = false;

    private $types = [];

    public static function fromReflection(ValueObject $valueObject, ReflectionProperty $reflectionProperty)
    {
        return new self($valueObject, $reflectionProperty);
    }

    public function __construct(ValueObject $valueObject, ReflectionProperty $reflectionProperty)
    {
        parent::__construct($reflectionProperty->class, $reflectionProperty->getName());

        $this->valueObject = $valueObject;

        $this->resolveTypeDefinition();
    }

    public function hasTypeDeclaration(): bool
    {
        return $this->hasTypeDeclaration;
    }

    public function isValidType($value): bool
    {
        $isValidType = false;

        if ($this->isNullable && $value === null) {
            return true;
        }

        foreach ($this->types as $currentType) {
            $isValidType = $this->assertValidType($currentType, $value);

            if ($isValidType) {
                break;
            }
        }

        return $isValidType;
    }

    public function set($value)
    {
        $isValidType = $this->isValidType($value);

        if (! $isValidType) {
            $expectedTypes = implode(', ', $this->getAvailableTypes());

            throw ValueObjectException::invalidType(
                $this->getName(),
                $this->getDeclaringClass()->getName(),
                $expectedTypes,
                $value
            );
        }

        $this->isInitialised = true;

        $this->valueObject->{$this->getName()} = $value;
    }

    public function getAvailableTypes(): array
    {
        return $this->types;
    }

    private function resolveTypeDefinition()
    {
        $docComment = $this->getDocComment();

        if (! $docComment) {
            return;
        }

        preg_match('/\@var ([\w|\\\\]+)/', $docComment, $matches);

        if (! count($matches)) {
            return true;
        }

        $this->hasTypeDeclaration = true;

        $varDocComment = end($matches);

        $this->types = explode('|', $varDocComment);

        $this->isNullable = strpos($varDocComment, 'null') !== false;
    }

    private function assertValidType(string $type, $value): bool
    {
        if ($this->isNullable && $value === null) {
            return true;
        }

        if ($type === 'mixed' && $value !== null) {
            return true;
        }

        if (class_exists($type)) {
            return $value instanceof $type;
        }

        return gettype($value) === (self::$typeMapping[$type] ?? $type);
    }
}
