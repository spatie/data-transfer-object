<?php

namespace Spatie\DataTransferObject\Reflection;

use JetBrains\PhpStorm\Immutable;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Attributes\DefaultCast;
use Spatie\DataTransferObject\Attributes\MapFrom;
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

        $this->name = $this->resolveMappedProperty();

        $this->caster = $this->resolveCaster();
    }

    public function setValue(mixed $value): void
    {
        if ($this->caster && $value !== null) {
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
            fn (ReflectionAttribute $attribute) => $attribute->newInstance(),
            $attributes
        );
    }

    public function getValue(): mixed
    {
        return $this->reflectionProperty->getValue($this->dataTransferObject);
    }

    public function getDefaultValue(): mixed
    {
        return $this->reflectionProperty->getDefaultValue();
    }

    private function resolveCaster(): ?Caster
    {
        $attributes = $this->reflectionProperty->getAttributes(CastWith::class);

        if (! count($attributes)) {
            $attributes = $this->resolveCasterFromType();
        }

        if (! count($attributes)) {
            return $this->resolveCasterFromDefaults();
        }

        /** @var \Spatie\DataTransferObject\Attributes\CastWith $attribute */
        $attribute = $attributes[0]->newInstance();

        return new $attribute->casterClass(
            array_map(fn ($type) => $this->resolveTypeName($type), $this->extractTypes()),
            ...$attribute->args
        );
    }

    private function resolveCasterFromType(): array
    {
        foreach ($this->extractTypes() as $type) {
            $name = $this->resolveTypeName($type);

            if (! class_exists($name)) {
                continue;
            }

            $reflectionClass = new ReflectionClass($name);

            do {
                $attributes = $reflectionClass->getAttributes(CastWith::class);

                $reflectionClass = $reflectionClass->getParentClass();
            } while (! count($attributes) && $reflectionClass);

            if (count($attributes) > 0) {
                return $attributes;
            }
        }

        return [];
    }

    private function resolveCasterFromDefaults(): ?Caster
    {
        $defaultCastAttributes = [];

        $class = $this->reflectionProperty->getDeclaringClass();

        do {
            array_push($defaultCastAttributes, ...$class->getAttributes(DefaultCast::class));

            $class = $class->getParentClass();
        } while ($class !== false);

        if (! count($defaultCastAttributes)) {
            return null;
        }

        foreach ($defaultCastAttributes as $defaultCastAttribute) {
            /** @var \Spatie\DataTransferObject\Attributes\DefaultCast $defaultCast */
            $defaultCast = $defaultCastAttribute->newInstance();

            if ($defaultCast->accepts($this->reflectionProperty)) {
                return $defaultCast->resolveCaster();
            }
        }

        return null;
    }

    private function resolveMappedProperty(): string | int
    {
        $attributes = $this->reflectionProperty->getAttributes(MapFrom::class);

        if (! count($attributes)) {
            return $this->reflectionProperty->name;
        }

        return $attributes[0]->newInstance()->name;
    }

    /**
     * @return ReflectionNamedType[]
     */
    private function extractTypes(): array
    {
        $type = $this->reflectionProperty->getType();

        if (! $type) {
            return [];
        }

        return match ($type::class) {
            ReflectionNamedType::class => [$type],
            ReflectionUnionType::class => $type->getTypes(),
        };
    }

    private function resolveTypeName(ReflectionType $type): string
    {
        return match ($type->getName()) {
            'self' => $this->dataTransferObject::class,
            'parent' => get_parent_class($this->dataTransferObject),
            default => $type->getName(),
        };
    }
}
