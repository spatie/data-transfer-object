<?php

namespace Spatie\DataTransferObject\Reflection;

use JetBrains\PhpStorm\Immutable;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Caster;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Validator;

class DataTransferObjectProperty
{
    #[Immutable]
    public string $name;

    private DataTransferObject $dataTransferObject;

    private ReflectionProperty $reflectionProperty;

    private ?Caster $caster;

    public function __construct(
        DataTransferObject $dataTransferObject,
        ReflectionProperty $reflectionProperty
    ) {
        $this->dataTransferObject = $dataTransferObject;
        $this->reflectionProperty = $reflectionProperty;

        $this->name = $this->reflectionProperty->name;

        $this->caster = $this->resolveCaster();
    }

    public function setValue(mixed $value): void
    {
        if ($this->caster) {
            $value = $this->caster->cast($value);
        }

        $this->reflectionProperty->setValue($this->dataTransferObject, $value);
    }

    /**
     * @return \Spatie\DataTransferObject\Validator[]
     */
    public function getValidators(): array
    {
        $attributes = $this->reflectionProperty->getAttributes(
            Validator::class,
            ReflectionAttribute::IS_INSTANCEOF
        );

        return array_map(
            fn(ReflectionAttribute $attribute) => $attribute->newInstance(),
            $attributes
        );
    }

    public function getValue(): mixed
    {
        return $this->reflectionProperty->getValue($this->dataTransferObject);
    }

    private function resolveCaster(): ?Caster
    {
        $attributes = $this->reflectionProperty->getAttributes(CastWith::class);

        if (! count($attributes)) {
            $attributes = $this->resolveCasterFromType();
        }

        if (! count($attributes)) {
            return null;
        }

        /** @var \Spatie\DataTransferObject\Attributes\CastWith $attribute */
        $attribute = $attributes[0]->newInstance();

        return new $attribute->casterClass(
            $this->reflectionProperty->getType()?->getName()
        );
    }

    private function resolveCasterFromType(): array
    {
        $type = $this->reflectionProperty->getType();

        if (! $type) {
            return [];
        }

        if (! class_exists($type->getName())) {
            return [];
        }

        $reflectionClass = new ReflectionClass($type->getName());

        do {
            $attributes = $reflectionClass->getAttributes();

            $reflectionClass = $reflectionClass->getParentClass();
        } while (! count($attributes) && $reflectionClass);

        return $attributes;
    }
}
