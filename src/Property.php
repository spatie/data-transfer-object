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

    /** @var bool */
    private $hasTypeDeclaration = false;

    /** @var bool */
    private $isNullable = false;

    /** @var bool */
    private $isInitialised = false;

    /** @var array */
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

    public function set($value)
    {
        if (! $this->isValidType($value)) {
            $expectedTypes = implode(', ', $this->types);

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

    private function resolveTypeDefinition()
    {
        $docComment = $this->getDocComment();

        if (! $docComment) {
            return;
        }

        preg_match('/\@var ([\w|\\\\]+)/', $docComment, $matches);

        if (! count($matches)) {
            return;
        }

        $this->hasTypeDeclaration = true;

        $varDocComment = end($matches);

        $this->types = explode('|', $varDocComment);

        $this->isNullable = strpos($varDocComment, 'null') !== false;
    }

    private function isValidType($value): bool
    {
        if (! $this->hasTypeDeclaration) {
            return true;
        }

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

    private function assertValidType(string $type, $value): bool
    {
        if ($type === 'mixed' && $value !== null) {
            return true;
        }

        if (class_exists($type)) {
            return $value instanceof $type;
        }

        return gettype($value) === (self::$typeMapping[$type] ?? $type);
    }
}
