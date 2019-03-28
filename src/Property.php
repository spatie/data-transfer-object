<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ReflectionProperty;
use Spatie\DataTransferObject\Contracts\DtoContract;
use Spatie\DataTransferObject\Contracts\PropertyContract;
use Spatie\DataTransferObject\Exceptions\InvalidTypeDtoException;

class Property implements PropertyContract
{
    /** @var array */
    protected static $typeMapping = [
        'int' => 'integer',
        'bool' => 'boolean',
        'float' => 'double',
    ];

    /** @var bool */
    protected $hasTypeDeclaration = false;

    /** @var bool */
    protected $nullable = false;

    /** @var bool */
    protected $initialised = false;

    /** @var bool */
    protected $immutable = false;

    /** @var bool */
    protected $visible = true;

    /** @var array */
    protected $types = [];

    /** @var array */
    protected $arrayTypes = [];

    /** @var mixed */
    protected $default;

    /** @var mixed */
    protected $value;

    /** @var ReflectionProperty */
    protected $reflection;

    public static function fromReflection(ReflectionProperty $reflectionProperty): self
    {
        return new static($reflectionProperty);
    }

    public function __construct(ReflectionProperty $reflectionProperty)
    {
        $this->reflection = $reflectionProperty;

        $this->resolveTypeDefinition();
    }

    protected function resolveTypeDefinition()
    {
        $docComment = $this->reflection->getDocComment();

        if (! $docComment) {
            $this->setNullable(true);

            return;
        }

        preg_match('/\@var ((?:(?:[\w|\\\\])+(?:\[\])?)+)/', $docComment, $matches);

        if (! count($matches)) {
            $this->setNullable(true);

            return;
        }

        $varDocComment = end($matches);

        $this->types = explode('|', $varDocComment);
        $this->arrayTypes = str_replace('[]', '', $this->types);

        if (in_array('immutable', $this->types) || in_array('Immutable', $this->types)) {
            $this->setImmutable(true);
            unset($this->types['immutable'], $this->types['Immutable']);

            if (empty($this->types)) {
                return;
            }
        }

        $this->hasTypeDeclaration = true;

        $this->setNullable(strpos($varDocComment, 'null') !== false);
    }

    protected function isValidType($value): bool
    {
        if (! $this->hasTypeDeclaration) {
            return true;
        }

        if ($this->nullable() && $value === null) {
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
            if (! is_subclass_of($type, DtoContract::class)) {
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
            if (! is_subclass_of($type, DtoContract::class)) {
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

    public function set($value) :void
    {
        if (is_array($value)) {
            $value = $this->shouldBeCastToCollection($value) ? $this->castCollection($value) : $this->cast($value);
        }

        if (! $this->isValidType($value)) {
            throw new InvalidTypeDtoException($this, $value);
        }

        $this->setInitialized(true);

        $this->value = $value;
    }

    public function setInitialized(bool $bool):void
    {
        $this->initialised = $bool;
    }

    public function isInitialized() :bool
    {
        return $this->initialised;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function getFqn(): string
    {
        return "{$this->reflection->getDeclaringClass()->getName()}::{$this->reflection->getName()}";
    }

    public function nullable(): bool
    {
        return $this->nullable;
    }

    public function setNullable(bool $bool): void
    {
        $this->nullable = $bool;
    }

    public function immutable(): bool
    {
        return $this->immutable;
    }

    public function setImmutable(bool $immutable): void
    {
        $this->immutable = $immutable;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function setDefault($default): void
    {
        $this->default = $default;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $bool): bool
    {
        return $this->visible = $bool;
    }

    public function getValue()
    {
        if (! $this->nullable() && $this->value == null) {
            return $this->getDefault();
        }

        return $this->value;
    }

    public function getValueFromReflection($object)
    {
        return $this->reflection->getValue($object);
    }

    public function getName() :string
    {
        return $this->reflection->getName();
    }

    public function getReflection(): ReflectionProperty
    {
        return $this->reflection;
    }
}
